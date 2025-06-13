<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Support\Contracts\HasFilamentTabs;
use Exception;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait UsesStatusTabs
{
    private Model $model;
    private static string $statusColumn;

    /**
     * @return array<string | int, Tab>
     *
     * @throws Exception
     */
    public function getTabs(): array
    {
        $model = app($this->getModel());

        if (! $model instanceof HasFilamentTabs) {
            return [];
        }

        self::$statusColumn = method_exists($model, 'getFilamentTabColumn')
            ? $model->getFilamentTabColumn()
            : 'status';

        $states = collect($model->getFilamentTabs())->unique();
        $final = $model->finalSuccessState();

        $this->calculateCounts(asInstanceOf($model, Model::class), arrayOfStrings($states->all()));

        return $states->map(fn ($state) => $this->tab(asString($state), $state === $final))
            ->prepend($this->allRecordsTab())->push($this->othersTab($states->all()))
            ->collapse()->all();
    }

    /**
     * @return array<string, Tab>
     */
    private function tab(string $state, bool $final): array
    {
        return [
            $state => Tab::make(headline($state))->icon($final ? 'heroicon-o-sparkles' : null)
                ->modifyQueryUsing(fn (Builder $query) => $query->where(self::$statusColumn, $state))
                ->badge(asInteger($this->model->getAttribute(str($state)->snake()->toString())))
                ->badgeColor('gray'),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    private function allRecordsTab(): array
    {
        return [
            'all' => Tab::make()->icon('heroicon-o-rectangle-stack')
                ->badge(asInteger($this->model->getAttribute('total'))),
        ];
    }

    /**
     * @param  array<string>  $states
     */
    private function calculateCounts(Model $model, array $states): void
    {
        $column = self::$statusColumn;
        $selects = ['COUNT(*) as total'];

        foreach ($states as $state) {
            $alias = str($state)->snake()->toString();
            $selects[] = "COUNT(CASE WHEN {$column} = ? THEN 1 END) AS {$alias}";
        }

        $placeholders = implode(',', array_fill(0, count($states), '?'));
        $selects[] = "COUNT(CASE WHEN {$column} NOT IN ({$placeholders}) THEN 1 END) AS others";

        $bindings = [...$states, ...$states];

        $this->model = $model->newQuery()->selectRaw(implode(', ', $selects), $bindings)->firstOrFail();
    }

    /**
     * @param  array<string>  $states
     *
     * @return array<string, Tab>
     */
    private function othersTab(array $states): array
    {
        return [
            'others' => Tab::make('Others')
                ->badge(asInteger($this->model->getAttribute('others')))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn(self::$statusColumn, $states))
                ->badgeColor('gray'),
        ];
    }
}
