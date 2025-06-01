<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

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

    public function getIcon(): string
    {
        return 'heroicon-o-plus-circle';
    }
}
