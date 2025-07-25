<?php

declare(strict_types=1);

namespace Atendwa\Filakit;

use Atendwa\Filakit\Concerns\SharedTableConfiguration;

class Resource extends \Filament\Resources\Resource
{
    use SharedTableConfiguration;

    public static ?string $pluginName = null;

    public static array $relations = [];

    protected static ?string $defaultRecordTitleAttribute = 'name';

    public static function getRelations(): array
    {
        return self::$relations;
    }
}
