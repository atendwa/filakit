<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Table\Actions;

use Atendwa\Filakit\Concerns\CustomizesCreateActions;

class CreateAction extends \Filament\Tables\Actions\CreateAction
{
    use CustomizesCreateActions;

    public function isModalSlideOver(): bool
    {
        return true;
    }
}
