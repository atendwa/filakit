<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Support\Contracts\Transitionable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait CustomizesResourceTable
{
    protected static Table $customTable;

    /**
     * @throws Throwable
     */
    protected static function customTable(): Table
    {
        $model = asInstanceOf(app(self::$customTable->getModel()), Model::class);

        when(self::$configureTableColumns, fn () => self::configureColumns($model));
        when(self::$configureTableFilters, fn () => self::configureFilters($model));

        when(
            self::$hasViewAction,
            fn (): Table => self::$customTable
                ->actions(array_merge(self::$customTable->getActions(), [viewRecordAction(self::$panel)]))
        );

        return self::$customTable->recordUrl(null);
    }

    protected static function useTenantOwnershipFilter(): bool
    {
        return false;
    }

    /**
     * @throws Throwable
     */
    private static function configureFilters(Model $model): void
    {
        $filters = collect(self::$customTable->getFilters());

        when(self::$useDateFilter, fn () => $filters->prepend(
            date_filter(self::dateFilterStartDate(), self::dateFilterEndDate())
        ));

        when(
            self::useTenantOwnershipFilter(),
            fn () => $filters->push(relation_filter(filament()->getTenantOwnershipRelationshipName()))
        );

        when(
            $model instanceof Transitionable && self::$useStatusFilter,
            fn () => $filters->push(status_filter())
        );

        when(self::$useIsActiveFilter, fn () => $filters->push(isActive_filter()));

        self::$customTable->filters($filters->values()->all());
    }

    private static function configureColumns(Model $model): void
    {
        $key = $model->getRouteKeyName();

        $columns = collect(self::$customTable->getColumns());

        when(self::$showRecordRouteKeyColumn, fn () => $columns->prepend(
            column($key, true)->label('Identifier')
        ));

        when(self::$hasCreatedAtColumn, fn () => $columns->push(
            column('created_at', true)->dateTime('D, d M Y, H:m')
        ));

        when(self::$hasUpdatedAtColumn, fn () => $columns->push(
            column('updated_at', true)->dateTime('D, d M Y, H:m')
        ));

        self::$customTable->columns($columns->all());
    }
}
