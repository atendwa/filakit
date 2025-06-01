<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Actions;

use Atendwa\Filakit\Concerns\CustomizesEditActions;

class EditAction extends \Filament\Actions\EditAction
{
    use CustomizesEditActions;
}
