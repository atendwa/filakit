<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Support\Contracts\Transitionable;
use Exception;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait UsesStatusTabs
{
    private Model $model;

    /**
     * @return array<string | int, Tab>
     *
     * @throws Exception
     */
    public function getTabs(): array
    {
        $model = app($this->getModel());

        if (! $model instanceof Transitionable) {
            return [];
        }

        $states = collect($model->states())->map(fn ($state, $key): array => [$key, $state])->collapse()->unique();
        $final = $model->finalSuccessState();

        $this->calculateCounts(asInstanceOf($model, Model::class), arrayOfStrings($states->all()));

        return $states->map(fn ($state) => $this->tab(asString($state), $state === $final))
            ->prepend($this->allRecordsTab())->collapse()->all();
    }

    /**
     * @return array<string, Tab>
     */
    private function tab(string $state, bool $final): array
    {
        return [
            $state => Tab::make(headline($state))->icon($final ? 'heroicon-o-sparkles' : null)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $state))
                ->badge(asInteger($this->model->getAttribute(str($state)->snake()->toString())))
                ->badgeColor('gray'),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    private function allRecordsTab(): array
    {
        return [
            'all' => Tab::make()->icon('heroicon-o-rectangle-stack')
                ->badge(asInteger($this->model->getAttribute(''))),
        ];
    }

    /**
     * @param  array<string>  $states
     */
    private function calculateCounts(Model $model, array $states): void
    {
        $selects = ['COUNT(*) as total'];

        foreach ($states as $state) {
            $selects[] = str("COUNT(CASE WHEN status = ':state' THEN 1 END) AS :alias")
                ->replace([':state', ':alias'], [$state, str($state)->snake()->toString()])
                ->toString();
        }

        $this->model = $model->newQuery()->selectRaw(implode(', ', $selects))->firstOrFail();
    }
}
