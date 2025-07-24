<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Pages;

use Atendwa\Filakit\Concerns\HandlesPageAuthorization;
use Atendwa\Filakit\Concerns\HasRefreshAction;
use Atendwa\Filakit\Concerns\InferResourceClassString;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Gate;
use function Filament\authorize;
use Throwable;

class ListRecords extends \Filament\Resources\Pages\ListRecords
{
    use HandlesPageAuthorization;
    use HasRefreshAction;
    use InferResourceClassString;

    protected bool $creatable = true;

    /**
     * @return array<int, Action|ActionGroup>
     *
     * @throws Throwable
     */
    protected function getHeaderActions(): array
    {
        $actions = [
            CreateAction::make()->visible($this->creatable()),
            $this->getRefreshAction(),
        ];

        return collect($this->actions())->merge($actions)->all();
    }

    /**
     * @return array<int, Action|ActionGroup>
     */
    protected function actions(): array
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    protected function creatable(): bool
    {
        return every([
            self::fetchResource()::hasPage('create'),
            Gate::allows('create', self::getModel()),
            $this->creatable,
        ]);
    }
}
