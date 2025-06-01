<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Actions;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

class Action extends \Filament\Actions\Action
{
    public function icon(Htmlable|string|Closure|null $icon): static
    {
        if (is_string($icon)) {
            $this->modalIcon = $icon;
        }

        $this->icon = $icon;

        return $this;
    }
}
