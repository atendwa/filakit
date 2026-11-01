<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
trait CustomizesEditActions
{
    public function getLabel(): string
    {
        return 'Update';
    }

    public function getColor(): string
    {
        return 'gray';
    }

    public function getIcon(string|BackedEnum|Htmlable|null $default = null): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-pencil-square';
    }
}
