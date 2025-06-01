<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use function Filament\authorize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Throwable;

trait HasRecordPageActions
{
    use HasRefreshAction;

    protected bool $deletable = false;

    /**
     * @throws Throwable
     */
    protected function editable(Model $model): bool
    {
        return authorize('update', $model)->allowed() && self::fetchResource()::hasPage('edit');
    }

    protected function restorable(Model $model): bool
    {
        return Gate::allows('restore', $model);
    }

    protected function destroyable(Model $model, string $ability = 'delete'): bool
    {
        return authorize($ability, $model)->allowed() && $this->deletable;
    }

    protected function getRefreshUrl(): string
    {
        return self::getUrl(['record' => $this->getRecord()->getRouteKey()]);
    }
}
