<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Pages;

use Atendwa\Filakit\Concerns\HandlesPageAuthorization;
use Atendwa\Filakit\Concerns\HasRecordPageActions;
use Atendwa\Filakit\Concerns\InferResourceClassString;
use Atendwa\Filakit\Concerns\WithBackToIndexAction;
use Atendwa\Filakit\Contracts\ModelHasIcon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Throwable;

class ViewRecord extends \Filament\Resources\Pages\ViewRecord
{
    use HandlesPageAuthorization;
    use HasRecordPageActions;
    use InferResourceClassString;
    use WithBackToIndexAction;

    /**
     * @var array<int, array<string, string>>
     */
    protected array $relations = [];

    /**
     * @return array<Action|ActionGroup>
     *
     * @throws Throwable
     */
    protected function getHeaderActions(): array
    {
        $model = $this->getRecord();

        $actions = collect()->push($this->getBackAction())->merge($this->actions())
            ->push(EditAction::make()->visible($this->editable($model)));

        $links = $this->getRelatedLinks();

        if (filled($links)) {
            $actions = $actions->push($links);
        }

        return $actions->push(DeleteAction::make()->visible($this->destroyable($model)))
            ->push(ForceDeleteAction::make()->visible($this->destroyable($model, 'force_delete')))
            ->push(RestoreAction::make()->visible($this->restorable($model)))
            ->push($this->getRefreshAction())->flatten()
            ->filter(fn ($action): bool => $action instanceof Action || $action instanceof ActionGroup)->all();
    }

    /**
     * @return array<int, Action|ActionGroup>
     */
    protected function actions(): array
    {
        return [];
    }

    protected function getRelatedLinks(): Action|ActionGroup|null
    {
        if (blank($this->relations)) {
            return null;
        }

        $actions = collect($this->relations)->map(fn ($relation): Action => $this->relatedAction($relation));

        return match ($actions->count() > 1) {
            true => ActionGroup::make($actions->all())->label('Related Records')->button(),
            false => $actions->first(),
        };
    }

    /**
     * @param  array<string, string>  $relation
     *
     * @throws Throwable
     */
    protected function relatedAction(array $relation): Action
    {
        $name = $relation['relation'];
        $model = $this->getRecord();
        $icon = null;

        $related = asInstanceOf($model->$name, Model::class);
        $relationship = modelRelation($model, $name);

        $label = [true => 'View ' . headline(class_basename($related)), false => 'View ' . headline($name)];

        if ($related instanceof ModelHasIcon) {
            $icon = $related->getIcon();
        }

        return Action::make(headline($name))->color('gray')->label($label[$relationship instanceof MorphTo])
            ->url(modelUrl($related, panelId: $relation['panel'] ?? null, routeKey: $related->getRouteKey()))
            ->icon($icon);
    }
}
