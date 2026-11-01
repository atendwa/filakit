<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Widgets;

class StatsOverviewWidget extends \Filament\Widgets\StatsOverviewWidget
{
    protected int|array|null $columns = 2;

    public static function isLazy(): bool
    {
        return false;
    }

    protected function getColumns(): int|array
    {
        return $this->columns ?? 2;
    }

    protected function getPollingInterval(): ?string
    {
        return null;
    }
}
