<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Pages;

use Atendwa\Filakit\Concerns\HandlesPageAuthorization;
use Atendwa\Filakit\Concerns\HasRecordPageActions;
use Atendwa\Filakit\Concerns\InferResourceClassString;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Throwable;

class EditRecord extends \Filament\Resources\Pages\EditRecord
{
    use HandlesPageAuthorization;
    use HasRecordPageActions;
    use InferResourceClassString;

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        $model = $this->getRecord();

        return [
            Action::make('back')->action(fn () => $this->redirect($this->getRedirectUrl(), true))
                ->icon('heroicon-o-arrow-long-left')->color('gray')->label('Go back'),
            DeleteAction::make()->authorize($this->destroyable($model)),
            ForceDeleteAction::make()->authorize($this->destroyable($model, 'force_delete')),
            RestoreAction::make()->authorize($this->restorable($model)),
            $this->getRefreshAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        $icon = 'heroicon-o-check-circle';

        return parent::getSaveFormAction()->icon($icon)->color('success')->label('Save');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->icon('heroicon-o-arrow-long-left')->label('Go back');
    }

    protected function getSavedNotificationTitle(): string
    {
        return 'Record updated successfully!';
    }

    /**
     * @throws Throwable
     */
    protected function getRedirectUrl(): string
    {
        return asString(self::fetchResource()::getUrl('view', ['record' => $this->getRecord()->getRouteKey()]));
    }
}
