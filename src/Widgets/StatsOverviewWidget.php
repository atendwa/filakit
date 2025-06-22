<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Widgets;

use Atendwa\Filakit\Concerns\UsesLazyWidget;

class StatsOverviewWidget extends \Filament\Widgets\StatsOverviewWidget
{
    use UsesLazyWidget;

    public int $columns = 2;

    protected function getColumns(): int
    {
        return $this->columns;
    }
}
