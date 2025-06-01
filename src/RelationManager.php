<?php

declare(strict_types=1);

namespace Atendwa\Filakit;

use Atendwa\Filakit\Concerns\SharedTableConfiguration;
use Atendwa\Filakit\Contracts\ModelHasIcon;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class RelationManager extends \Filament\Resources\RelationManagers\RelationManager
{
    use SharedTableConfiguration;

    protected static bool $isLazy = false;

    /**
     * @throws Throwable
     */
    public static function getIcon(Model $ownerRecord, string $pageClass): ?string
    {
        throw_if(! class_exists($pageClass));

        if (filled(static::$icon)) {
            return static::$icon;
        }

        $model = relatedModel($ownerRecord, self::getRelationshipName());

        if ($model instanceof ModelHasIcon) {
            return $model->getIcon();
        }

        return null;
    }
}
