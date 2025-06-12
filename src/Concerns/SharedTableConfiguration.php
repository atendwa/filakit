<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Illuminate\Support\Carbon;

trait SharedTableConfiguration
{
    protected static bool $useTenantOwnershipFilter = false;

    protected static bool $showRecordRouteKeyColumn = true;

    protected static bool $configureTableColumns = true;

    protected static bool $configureTableFilters = true;

    protected static bool $hasUpdatedAtColumn = true;

    protected static bool $hasCreatedAtColumn = true;

    protected static bool $useIsActiveFilter = true;

    protected static bool $useStatusFilter = true;

    protected static bool $useDateFilter = false;

    protected static bool $hasViewAction = true;

    protected static ?string $panel = null;

    protected static function dateFilterStartDate(): ?Carbon
    {
        return null;
    }

    protected static function dateFilterEndDate(): ?Carbon
    {
        return null;
    }
}
