<?php

declare(strict_types=1);

namespace Atendwa\Filakit;

use Atendwa\Filakit\Concerns\SharedTableConfiguration;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Illuminate\Database\Eloquent\Model;

class Resource extends \Filament\Resources\Resource
{
    use SharedTableConfiguration;

    public static ?string $pluginName = null;

    /**
     * @var array<class-string<RelationManager>|RelationGroup|RelationManagerConfiguration>
     */
    public static array $relations = [];

    protected static ?string $defaultRecordTitleAttribute = 'name';

    /**
     * @throws Throwable
     */
    protected static function model(): Model
    {
        return asInstanceOf(app(self::getModel()), Model::class);
    }
}
