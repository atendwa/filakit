<?php

declare(strict_types=1);

use Atendwa\Filakit\Components\TextColumn;
use Atendwa\Filakit\Components\TextEntry;
use Atendwa\Filakit\Components\TextInput;
use Atendwa\Filakit\Widgets\Stat;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('textEntry')) {
    function textEntry(string $column): TextEntry
    {
        return TextEntry::make($column)->label(str($column)->replace('.', ' ')->headline()->toString());
    }
}

if (! function_exists('textInput')) {
    function textInput(string $name, bool $required = true): TextInput
    {
        return TextInput::make($name)->maxLength(255)->required($required)->label(headline($name));
    }
}

if (! function_exists('column')) {
    function column(
        string $column,
        bool $toggleable = false,
        bool $hidden = true,
        bool $sortable = true
    ): TextColumn {
        return TextColumn::make($column)->searchable($sortable)->toggleable($toggleable, $hidden)
            ->sortable($sortable)->label(str($column)->headline()->toString());
    }
}

if (! function_exists('textArea')) {
    function textArea(string $name): Textarea
    {
        return Textarea::make($name);
    }
}

if (! function_exists('toggle')) {
    function toggle(string $name = 'is_active', bool $default = true): Toggle
    {
        return Toggle::make($name)->label(headline($name))->default($default);
    }
}

if (! function_exists('isActive')) {
    function isActive(string $column = 'is_active'): TextColumn
    {
        return column($column)->badge()->label('Status')
            ->formatStateUsing(fn (bool $state): string => $state ? 'Enabled' : 'Disabled')
            ->color(fn (bool $state): string => $state ? 'success' : 'danger');
    }
}

if (! function_exists('badgeColumn')) {
    /**
     * @param  array<string>  $states
     * @param  array<string>  $colours
     */
    function badgeColumn(
        string $column,
        string $expectation,
        array $states,
        array $colours = ['success', 'danger']
    ): TextColumn {
        return column($column)->badge()->label(headline($column))
            ->formatStateUsing(fn (string $state) => $state === $expectation ? $states[0] : $states[1])->headline()
            ->color(fn (string $state) => $state === $expectation ? $colours[0] : $colours[1]);
    }
}

if (! function_exists('badgeEntry')) {
    /**
     * @param  array<string>  $states
     * @param  array<string>  $colours
     */
    function badgeEntry(
        string $column,
        string $expectation,
        array $states,
        array $colours = ['success', 'danger']
    ): TextEntry {
        return textEntry($column)->badge()->label(ucfirst($column))
            ->formatStateUsing(fn (string $state) => $state === $expectation ? $states[0] : $states[1])->headline()
            ->color(fn (string $state) => $state === $expectation ? $colours[0] : $colours[1]);
    }
}

if (! function_exists('select')) {
    function select(string $name, bool $required = true): Select
    {
        $placeholder = str($name)->before('_id')->headline()->lower()->toString();

        return Select::make($name)->required($required)->label(headline($placeholder))->placeholder(
            fn ($context): string => $context == 'view' ? 'N/A' : 'Select ' . $placeholder
        );
    }
}

if (! function_exists('hidden')) {
    function hidden(string $name, bool $required = true): Hidden
    {
        return Hidden::make($name)->required($required);
    }
}

if (! function_exists('status')) {
    function status(string $name = 'status'): TextColumn
    {
        return column($name)->status();
    }
}

if (! function_exists('relationship')) {
    function relationship(string $relation, string $title = 'name', bool $required = true, ?string $key = null): Select
    {
        $name = $key ?? str($relation)->append('_id')->toString();

        return select($name, $required)->relationship($relation, $title);
    }
}

if (! function_exists('viewRecordAction')) {
    function viewRecordAction(?string $panel = null, string $route = 'view', ?string $relation = null): ViewAction
    {
        $name = str('view-' . $relation . '-action')->replace('--', '-')->toString();
        $action = ViewAction::make($name)->label(trim(headline('View ' . $relation)));

        return $action
            ->url(function ($record) use ($relation, $route, $panel) {
                $record = determineActionModel($record, $relation);

                if (blank($record)) {
                    return null;
                }

                return modelUrl(($record), $route, $panel);
            })
            ->visible(function ($record) use ($relation) {
                $record = determineActionModel($record, $relation);

                return filled($record) && Gate::allows('view', $record);
            });
    }
}

if (! function_exists('determineActionModel')) {
    function determineActionModel(Model $model, ?string $relation = null): ?Model
    {
        if (blank($relation)) {
            return $model;
        }

        return $model->getRelationValue($relation);
    }
}

if (! function_exists('navigationWidget')) {
    function navigationWidget(string $label, string $url, ?string $text = null): Stat
    {
        return Stat::make('', $label)->description($text)->url($url);
    }
}

if (! function_exists('slugColumn')) {
    function slugColumn(string $column = 'slug', string $label = 'Code'): TextColumn
    {
        return column($column)->label($label);
    }
}
