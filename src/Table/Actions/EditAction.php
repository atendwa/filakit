<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Table\Actions;

use Filament\Actions\Action;
use Atendwa\Filakit\Concerns\CustomizesEditActions;
use Filament\Actions\StaticAction;

class EditAction extends \Filament\Actions\EditAction
{
    use CustomizesEditActions;

    public function isModalSlideOver(): bool
    {
        return true;
    }

    public function getModalCancelAction(): Action
    {
        return static::makeModalAction('Close')->close()->color('gray');
    }

    public function getModalSubmitAction(): ?Action
    {
        $action = static::makeModalAction('submit')->color('success')
            ->icon('heroicon-o-check-circle')->label('Save')
            ->submit($this->getLivewireCallMountedActionName());

        if ($this->modalSubmitAction !== null) {
            $action = $this->evaluate($this->modalSubmitAction, ['action' => $action]) ?? $action;
        }

        return $action instanceof Action ? $action : null;
    }
}
