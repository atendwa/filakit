<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
trait CustomizesCreateActions
{
    public function getLabel(): string
    {
        $label = $this->evaluate($this->label);

        return str(is_string($label) ? $label : '')->headline()->toString();
    }

    public function getColor(): string
    {
        return 'gray';
    }

    public function getIcon(string|BackedEnum|Htmlable|null $default = null): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-plus-circle';
    }
}
