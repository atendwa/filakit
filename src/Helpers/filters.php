<?php

declare(strict_types=1);

use Atendwa\Support\Contracts\Toggleable;
use Atendwa\Support\Contracts\Transitionable;
use Atendwa\Support\Services\FindClassesUsingTrait;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

if (! function_exists('isActive_filter')) {
    /**
     * @throws Exception
     */
    function isActive_filter(): BaseFilter
    {
        return TernaryFilter::make('is_active')->label('Active Status')
            ->falseLabel('Inactive')->trueLabel('Active')
            ->placeholder('All')->visible(function (Table $table): bool {
                $model = app($table->getModel());

                if ($model instanceof Toggleable) {
                    return $model->isToggleable();
                }

                return true;
            });
    }
}

if (! function_exists('typeFilter')) {
    /**
     * @param  class-string  $trait
     *
     * @throws Exception
     */
    function typeFilter(string $name, string $trait): SelectFilter
    {
        return SelectFilter::make($name)->searchable()->options(function () use ($trait) {
            $classes = app(FindClassesUsingTrait::class)->execute($trait, app_path());

            return collect($classes)->mapWithKeys(fn ($class) => [$class => headline(class_basename($class))])->all();
        });
    }
}

if (! function_exists('status_filter')) {
    /**
     * @throws Exception
     */
    function status_filter(): BaseFilter
    {
        return SelectFilter::make('status')
//            ->visible(fn (Table $table): bool => app($table->getModel()) instanceof Transitionable)
            ->options(function (Table $table) {
                $model = app($table->getModel());

                if (! $model instanceof Transitionable) {
                    return [];
                }

                $transitions = $model->states();

                return collect(array_keys($transitions))->merge(array_values($transitions))->unique()
                    ->mapWithKeys(fn ($status) => [$status => str($status)->headline()->toString()])
                    ->filter()->all();
            });
    }
}

if (! function_exists('date_filter')) {
    /**
     * @throws Exception
     */
    function date_filter(?Carbon $start = null, ?Carbon $end = null): Filter
    {
        return Filter::make('date_filter')->columnSpan(2)->columnSpanFull()->columns()->form([
            DatePicker::make('start_date')->label('Start Date')->default($start ?? today()->startOfYear()),
            DatePicker::make('end_date')->label('End Date')->default($end ?? today()->endOfDay()),
        ])->indicateUsing(function (array $data): array {
            $start = $data['start_date'];
            $end = $data['end_date'];
            $indicators = [];

            if (is_string($start)) {
                $indicators[] = Indicator::make('From: ' . carbon()->parse($start)->toFormattedDateString())
                    ->removeField('start_date');
            }

            if (is_string($end)) {
                $indicators[] = Indicator::make('To: ' . carbon()->parse($end)->toFormattedDateString())
                    ->removeField('end_date');
            }

            return $indicators;
        })
            ->query(
                fn (Builder $builder, array $data) => $builder
                    ->when(
                        is_string($data['start_date']),
                        fn ($query) => $query->whereDate('created_at', '>=', asString($data['start_date'])),
                    )
                    ->when(
                        is_string($data['end_date']),
                        fn ($query) => $query->whereDate('created_at', '<=', asString($data['end_date'])),
                    )
            );
    }
}

if (! function_exists('relation_filter')) {
    /**
     * @throws Exception
     */
    function relation_filter(
        string $relation,
        bool $preload = true,
        string $title = 'name',
        ?string $label = null,
        ?Closure $query = null
    ): SelectFilter {
        return SelectFilter::make($relation)->label($label ?? headline($relation))->columnSpanFull()
            ->relationship($relation, $title, $query)->preload($preload)->searchable();
    }
}

if (! function_exists('select_filter')) {
    /**
     * @param  array<string, mixed>|array<string>  $options
     *
     * @throws Exception
     */
    function select_filter(string $column, array $options, ?string $label = null): SelectFilter
    {
        return SelectFilter::make($column)->label($label ?? headline($column))
            ->columnSpanFull()->searchable()->options(fn () => collect($options)
            ->mapWithKeys(fn ($option, $key) => [is_numeric($key) ? $option : $key => headline($option)])->all());
    }
}
