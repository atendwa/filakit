<?php

declare(strict_types=1);

namespace Atendwa\Filakit;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class Panel extends \Filament\Panel
{
    /**
     * @throws Throwable
     */
    public function getTenant(string $key): Model
    {
        $tenantModel = (string) $this->getTenantModel();

        $record = cache()->remember(
            modelCacheKey($tenantModel, $key),
            now()->addMinutes(asInteger(config('filakit.tenancy.panel_cache_minutes'))),
            fn () => asInstanceOf(app($tenantModel), Model::class)->resolveRouteBinding(
                $key,
                $this->getTenantSlugAttribute()
            )
        );

        return asInstanceOf($record, Model::class);
    }
}
