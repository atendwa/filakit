<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\View\View;

class NavigationLinkWidget extends Stat
{
    public function render(): View
    {
        // todo: compile my css

        return view('filakit::partials.quick-link', asMixedArray($this->data()));
    }
}
