<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait HandlesPageAuthorization
{
    /**
     * @param  array<string, mixed>  $parameters
     *
     * @throws Throwable
     */
    public static function canAccess(array $parameters = []): bool
    {
        return self::gate($parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     *
     * @throws Throwable
     */
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        if (filled(self::$shouldRegisterNavigation)) {
            return self::$shouldRegisterNavigation;
        }

        return self::gate($parameters);
    }

    protected static function block(): bool
    {
        return false;
    }

    /**
     * @param  array<string, mixed>  $parameters
     *
     * @throws Throwable
     */
    protected static function gate(array $parameters = []): bool
    {
        if (self::block()) {
            return false;
        }

        $record = $parameters['record'] ?? null;

        if (! $record instanceof Model) {
            $record = self::fetchModel();
        }

        $prefix = [true => 'view_any', false => 'view'][app(static::class) instanceof Resource];
        $permission = $prefix . '_' . permissionIdentifier($record, self::retrieveResource()::class);

        return auth()->user()?->can($permission) ?? false;
    }

    protected static function getGateAbility(): string
    {
        return match (str(static::class)->afterLast('\\')->snake()->explode('_')->first()) {
            default => 'view_any',
            'create' => 'create',
            'edit' => 'update',
            'view' => 'view'
        };
    }

    /**
     * @throws Throwable
     */
    protected static function fetchModel(): string
    {
        return asString(self::retrieveResource()::getModel());
    }

    /**
     * @throws Throwable
     */
    private static function retrieveResource(): Resource
    {
        $class = app(static::class);

        $resource = match (true) {
            $class instanceof Resource => $class::class,
            $class instanceof EditRecord, $class instanceof CreateRecord,
            $class instanceof ListRecords, $class instanceof ViewRecord => $class::getResource(),
            default => null
        };

        $resource = app($resource);

        throw_if(! $resource instanceof Resource, 'Resource not found for ' . static::class);

        return $resource;
    }
}
