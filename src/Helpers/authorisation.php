<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('resourcePermissions')) {
    /**
     * @return array<string>
     *
     * @throws Throwable
     */
    function resourcePermissions(Resource|string $resource): array
    {
        $resource = asObject($resource);

        $permissions = standardPermissions();

        if ($resource instanceof HasShieldPermissions) {
            $permissions = $resource->getPermissionPrefixes();
        }

        return arrayOfStrings($permissions);
    }
}

if (! function_exists('standardPermissions')) {
    /**
     * @return array<string>
     */
    function standardPermissions(): array
    {
        $permissions = config('filament-shield.permission_prefixes.resource');

        $permissions = match (is_array($permissions)) {
            true => $permissions,
            default => [],
        };

        return array_map(fn ($permission): string => asString($permission), $permissions);
    }
}

if (! function_exists('permissionIdentifier')) {
    /**
     * @throws Throwable
     */
    function permissionIdentifier(Model|string $model, ?string $resource = null, ?string $panel = null): string
    {
        return Str::of($resource ?? resource($model, $panel)::class)->afterLast('Resources\\')
            ->before('Resource')->replace('\\', '')
            ->snake()->replace('_', '::')->toString();
    }
}

if (! function_exists('permission')) {
    /**
     * @throws Throwable
     */
    function permission(string $prefix, ?string $resource = null, Model|string|null $model = null): string
    {
        throw_if(blank($resource) && blank($model), 'Resource / Model must be provided!');

        return $prefix . '_' . FilamentShield::getPermissionIdentifier($resource ?? resource($model)::class);
    }
}

if (! function_exists('can')) {
    /**
     * @throws Throwable
     */
    function can(string $permission, ?string $resource = null, Model|string|null $model = null, bool $qualify = true): bool
    {
        if ($qualify) {
            $permission = permission($permission, $resource, $model);
        }

        return auth()->user()?->can($permission) ?? false;
    }
}
