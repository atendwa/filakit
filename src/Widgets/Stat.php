<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Widgets;

use Illuminate\Contracts\View\View;

class Stat extends \Filament\Widgets\StatsOverviewWidget\Stat
{
    public function render(): View
    {
        return view('filakit::partials.stat', asMixedArray($this->data()));
    }
}
