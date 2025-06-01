<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

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

    public function getIcon(): string
    {
        return 'heroicon-o-pencil-square';
    }
}
