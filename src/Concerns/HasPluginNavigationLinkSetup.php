<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Filakit\Widgets\Stat;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait HasPluginNavigationLinkSetup
{
    public static function panelAccessPermission(): string
    {
        return str(static::class)->afterLast('\\')->before('PanelProvider')
            ->prepend('access')->append('_panel')->snake()->toString();
    }

    public static function canAccess(?Model $model = null, ?string $permission = null): bool
    {
        return ($model ?? auth()->user())?->can($permission ?? static::panelAccessPermission()) ?? false;
    }

    /**
     * @throws Throwable
     */
    public function toNavigationItem(): ?Stat
    {
        $panel = filament()->getPanel($this->getId());

        if (auth()->user()?->canAccessPanel($panel)) {
            $data = $this->featureData();

            return Stat::make($data['label'], $data['name'])->icon($data['icon'])->url(panelUrl($panel))
                ->color($data['colour'] ?? null)->description($data['description']);
        }

        return null;
    }

    public function featureName(): string
    {
        return $this->getName();
    }
}
