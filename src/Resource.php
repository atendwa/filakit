<?php

declare(strict_types=1);

namespace Atendwa\Filakit;

use Atendwa\Filakit\Concerns\SharedTableConfiguration;

class Resource extends \Filament\Resources\Resource
{
    use SharedTableConfiguration;

    public static ?string $pluginName = null;

    protected static ?string $defaultRecordTitleAttribute = 'name';
}
