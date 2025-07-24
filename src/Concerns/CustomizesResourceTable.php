<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Support\Contracts\Transitionable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait CustomizesResourceTable
{
    protected static Table $table;

    /**
     * @throws Throwable
     */
    protected static function customTable(): Table
    {
        $model = asInstanceOf(app(self::$table->getModel()), Model::class);

        when(self::$configureTableColumns, fn () => self::configureColumns($model));
        when(self::$configureTableFilters, fn () => self::configureFilters($model));

        when(
            self::$hasViewAction,
            fn (): Table => self::$table
                ->actions(array_merge(self::$table->getActions(), [viewRecordAction(self::$panel)]))
        );

        return self::$table->recordUrl(null);
    }

    /**
     * @throws Throwable
     */
    private static function configureFilters(Model $model): void
    {
        $filters = collect(self::$table->getFilters());

        when(self::$useDateFilter, fn () => $filters->prepend(
            date_filter(self::dateFilterStartDate(), self::dateFilterEndDate())
        ));

        when(
            self::$useTenantOwnershipFilter,
            fn () => $filters->push(relation_filter(filament()->getTenantOwnershipRelationshipName()))
        );

        when(
            $model instanceof Transitionable && self::$useStatusFilter,
            fn () => $filters->push(status_filter())
        );

        when(self::$useIsActiveFilter, fn () => $filters->push(isActive_filter()));

        self::$table->filters($filters->values()->all());
    }

    private static function configureColumns(Model $model): void
    {
        $key = $model->getRouteKeyName();

        $columns = collect(self::$table->getColumns());

        when(self::$showRecordRouteKeyColumn, fn () => $columns->prepend(
            column($key, true)->label('Identifier')
        ));

        when(self::$hasCreatedAtColumn, fn () => $columns->push(
            column('created_at', true)->dateTime('D, d M Y, H:m')
        ));

        when(self::$hasUpdatedAtColumn, fn () => $columns->push(
            column('updated_at', true)->dateTime('D, d M Y, H:m')
        ));

        self::$table->columns($columns->all());
    }
}
