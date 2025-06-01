<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Pages;

use Atendwa\Filakit\Concerns\HandlesPageAuthorization;
use Atendwa\Filakit\Concerns\HasRefreshAction;
use Atendwa\Filakit\Concerns\InferResourceClassString;
use Atendwa\Filakit\Concerns\WithBackToIndexAction;
use Filament\Actions\Action;

class CreateRecord extends \Filament\Resources\Pages\CreateRecord
{
    use HandlesPageAuthorization;
    use HasRefreshAction;
    use InferResourceClassString;
    use WithBackToIndexAction;

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackAction(),
            $this->getRefreshAction(),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->icon('heroicon-o-check')->color('success');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->icon('heroicon-o-document-duplicate');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->hidden();
    }

    protected function getSavedNotificationTitle(): string
    {
        return 'Record created successfully!';
    }
}
