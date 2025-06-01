<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Filament\Actions\Action;

trait WithBackToIndexAction
{
    protected function getBackAction(): Action
    {
        return Action::make('back')->label('Back to all')->color('gray')
            ->action(fn () => $this->redirect($this->backUrl(), true))
            ->icon('heroicon-o-arrow-long-left');
    }

    protected function backUrl(): string
    {
        return asString(self::$resource::getUrl());
    }
}
