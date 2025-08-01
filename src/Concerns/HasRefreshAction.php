<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Filament\Actions\Action;

trait HasRefreshAction
{
    protected function getRefreshAction(): Action
    {
        return Action::make('refresh')->icon('heroicon-o-arrow-path')->color('info')
            ->action(fn () => $this->redirect($this->getRefreshUrl(), true))->outlined()
            ->visible(filled($this->getRefreshUrl()))->label('Refresh');
    }

    protected function getRefreshUrl(): string
    {
        return self::getUrl();
    }

    protected function refresh(): void
    {
        $this->redirect($this->getRefreshUrl(), true);
    }
}
