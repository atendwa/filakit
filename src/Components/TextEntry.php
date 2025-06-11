<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Components;

class TextEntry extends \Filament\Infolists\Components\TextEntry
{
    public function headline(): TextEntry
    {
        return $this->formatStateUsing(fn (string $state): string => headline($state));
    }

    public function status(): TextEntry
    {
        return $this->badge()->color(fn ($record): string => $record->badgeColor())->headline();
    }
}
