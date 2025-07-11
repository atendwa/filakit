<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

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
        return Gate::allows('update', $model) && self::fetchResource()::hasPage('edit');
    }

    protected function restorable(Model $model): bool
    {
        if (! $model->hasAttribute('deleted_at')) {
            return false;
        }

        return Gate::allows('restore', $model) && filled($model->getAttribute('deleted_at'));
    }

    protected function destroyable(Model $model, string $ability = 'delete'): bool
    {
        return Gate::allows($ability, $model) && $this->deletable;
    }

    protected function getRefreshUrl(): string
    {
        return self::getUrl(['record' => $this->getRecord()->getRouteKey()]);
    }
}
