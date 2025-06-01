<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Table\Actions;

use Atendwa\Filakit\Concerns\CustomizesEditActions;
use Filament\Actions\StaticAction;

class EditAction extends \Filament\Tables\Actions\EditAction
{
    use CustomizesEditActions;

    public function isModalSlideOver(): bool
    {
        return true;
    }

    public function getModalCancelAction(): StaticAction
    {
        return static::makeModalAction('Close')->close()->color('gray');
    }

    public function getModalSubmitAction(): ?StaticAction
    {
        $action = static::makeModalAction('submit')->color('success')
            ->icon('heroicon-o-check-circle')->label('Save')
            ->submit($this->getLivewireCallMountedActionName());

        if ($this->modalSubmitAction !== null) {
            $action = $this->evaluate($this->modalSubmitAction, ['action' => $action]) ?? $action;
        }

        return $action instanceof StaticAction ? $action : null;
    }
}
