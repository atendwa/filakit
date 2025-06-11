<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Components;

use Atendwa\Support\Contracts\Transitionable;
use Closure;

class TextColumn extends \Filament\Tables\Columns\TextColumn
{
    public function headline(): static
    {
        return $this->formatStateUsing(fn (string $state): string => headline($state));
    }

    public function class(): static
    {
        return $this->formatStateUsing(fn (string $state) => class_basename($state));
    }

    public function badge(bool|Closure $condition = true): static
    {
        $this->color = 'gray';

        return parent::badge($condition);
    }

    public function status(): static
    {
        return $this->badge()->color(fn ($record): string => $record->badgeColor())->headline();
    }
}
