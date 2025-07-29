<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Widgets;

class StatsOverviewWidget extends \Filament\Widgets\StatsOverviewWidget
{
    public int $columns = 2;

    public static function isLazy(): bool
    {
        return false;
    }

    protected function getColumns(): int
    {
        return $this->columns;
    }

    protected function getPollingInterval(): ?string
    {
        return null;
    }
}
