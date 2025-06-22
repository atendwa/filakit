<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Widgets;

class StatsOverviewWidget extends \Filament\Widgets\StatsOverviewWidget
{
    public int $columns = 2;

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected function getColumns(): int
    {
        return $this->columns;
    }
}
