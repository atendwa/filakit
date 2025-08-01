<?php

namespace Atendwa\Filakit;

use Atendwa\Filakit\Concerns\HasRefreshAction;
use Atendwa\Filakit\Concerns\InferActiveNavigationIcon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

class Page extends \Filament\Pages\Page
{
    use HasPageShield;
    use HasRefreshAction;
    use InferActiveNavigationIcon;

    /**
     * @return Action[]|ActionGroup[]
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * @return Action[]|ActionGroup[]
     */
    protected function getHeaderActions(): array
    {
        return collect($this->actions())->push($this->getRefreshAction())->all();
    }
}
