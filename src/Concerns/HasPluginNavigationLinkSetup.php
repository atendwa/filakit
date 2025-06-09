<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Filakit\Contracts\HasNavigationLink;
use Atendwa\Filakit\Widgets\Stat;
use Filament\Models\Contracts\FilamentUser;
use Throwable;

trait HasPluginNavigationLinkSetup
{
    public static function panelAccessPermission(): string
    {
        return str(static::class)->afterLast('\\')->before('PanelProvider')
            ->prepend('access')->append('_panel')->snake()->toString();
    }

    /**
     * @throws Throwable
     */
    public function toNavigationItem(): ?Stat
    {
        $self = asInstanceOf($this, HasNavigationLink::class);

        $panel = filament()->getPanel($self->getId());

        $user = asInstanceOf(auth()->user(), FilamentUser::class);

        if ($user?->canAccessPanel($panel)) {
            $data = $self->featureData();

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
