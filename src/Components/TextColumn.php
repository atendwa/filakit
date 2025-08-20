<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Components;

use Closure;
use Illuminate\Support\Carbon;

class TextColumn extends \Filament\Tables\Columns\TextColumn
{
    public function headline(): static
    {
        return $this->formatStateUsing(fn (string $state): string => headline($state));
    }

    public function dayDate(): static
    {
        return $this->formatStateUsing(
            fn (?string $state = null) => filled($state) ? Carbon::parse($state)->toFormattedDayDateString() : 'N/A'
        );
    }

    public function empty(): static
    {
        return $this->formatStateUsing(fn ($state) => filled($state) ? $state : 'N/A');
    }

    public function class(): static
    {
        return $this->formatStateUsing(fn (string $state) => headline(class_basename($state)));
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
