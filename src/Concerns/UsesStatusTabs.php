<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Filakit\Contracts\HasFilamentTabs;
use Exception;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait UsesStatusTabs
{
    private Model $model;

    private static string $statusColumn;

    /**
     * @return array<string | int, Tab>
     *
     * @throws Throwable
     */
    public function getTabs(): array
    {
        $model = app($this->getModel());

        throw_if(! $model instanceof HasFilamentTabs, new Exception(
            'The model must implement the HasFilamentTabs contract to use status tabs.'
        ));

        self::$statusColumn = $model->getFilamentTabColumn();

        $icons = $model::getFilamentTabIcons();

        $states = collect($model->getFilamentTabs())->unique()->mapWithKeys(
            fn ($value, $key) => [is_numeric($key) ? $value : $key => is_array($value) ? $value : [$value]]
        );

        $fromStates = $states->map(
            fn ($state, $key) => Tab::make(headline($key))->icon($icons[$key] ?? null)->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn(self::$statusColumn, $state))
        );

        return collect($this->allRecordsTab($icons))->merge($fromStates)
            ->merge($this->othersTab($states->collapse()->all(), $icons))
            ->all();
    }

    /**
     * @return array<string, Tab>
     */
    private function allRecordsTab(array $icons): array
    {
        return [
            'all' => Tab::make()->icon($icons['all'] ?? null)->modifyQueryUsing(fn (Builder $query) => $query),
        ];
    }
    //
    //    /**
    //     * @param  array<string>  $states
    //     */
    //    private function calculateCounts(Model $model, array $states): void
    //    {
    //        $column = self::$statusColumn;
    //        $selects = ['COUNT(*) as total'];
    //
    //        foreach ($states as $state) {
    //            $alias = str($state)->snake()->toString();
    //            $selects[] = "COUNT(CASE WHEN {$column} = ? THEN 1 END) AS {$alias}";
    //        }
    //
    //        $placeholders = implode(',', array_fill(0, count($states), '?'));
    //        $selects[] = "COUNT(CASE WHEN {$column} NOT IN ({$placeholders}) THEN 1 END) AS others";
    //
    //        $bindings = [...$states, ...$states];
    //
    //        $this->model = $model->newQuery()->selectRaw(implode(', ', $selects), $bindings)->firstOrFail();
    //    }

    /**
     * @param  array<string>  $states
     *
     * @return array<string, Tab>
     */
    private function othersTab(array $states, array $icons): array
    {
        return [
            'others' => Tab::make('Others')->icon($icons['others'] ?? null)->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn(self::$statusColumn, $states)),
        ];
    }
}
