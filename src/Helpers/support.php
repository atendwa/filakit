<?php

declare(strict_types=1);

use Atendwa\Filakit\Contracts\ModelHasIcon;
use Atendwa\Filakit\Utils\FilamentNotifier;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Panel;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('activeTenant')) {
    function activeTenant(): ?Model
    {
        return Filament::getTenant();
    }
}

if (! function_exists('notify')) {
    function notify(Throwable|string|null $body = null): FilamentNotifier
    {
        return app(FilamentNotifier::class, ['body' => $body]);
    }
}

if (! function_exists('modelIcon')) {
    /**
     * @throws Throwable
     */
    function modelIcon(ModelHasIcon|string $model): string
    {
        $model = asObject($model);

        throw_if(! $model instanceof ModelHasIcon, 'Model does not implement ModelHasFilamentIcon');

        return $model->getIcon();
    }
}

if (! function_exists('resource')) {
    /**
     * @throws Throwable
     */
    function resource(Model|string $record, ?string $panelId = null): Resource
    {
        $panel = filament()->getCurrentPanel();

        if (filled($panelId)) {
            $panel = filament()->getPanel($panelId);
        }

        $class = $record;

        if ($record instanceof Model) {
            $class = $record->getMorphClass();
        }

        $resource = collect($panel?->getResources())->map(fn ($class) => app($class))
            ->filter(fn ($resource): bool => $resource instanceof Resource)
            ->filter(fn (Resource $resource): bool => $resource->getModel() === $class)
            ->first();

        throw_if(! $resource instanceof Resource, 'Resource not found for ' . $class);

        return $resource;
    }
}

if (! function_exists('activeTenantID')) {
    function activeTenantID(): ?int
    {
        $id = activeTenant()?->getKey();

        return is_numeric($id) ? (int) $id : null;
    }
}

if (! function_exists('modelUrl')) {
    /**
     * @throws Throwable
     */
    function modelUrl(Model|string $model, string $route = 'view', ?string $panelId = null, mixed $routeKey = null): string
    {
        $params = [];

        if ($model instanceof Model && blank($routeKey)) {
            $params['record'] = $model->getRouteKey();
        }

        if (filled($routeKey)) {
            $params['record'] = $routeKey;
        }

        return resource($model, $panelId)::getUrl($route, $params, panel: $panelId);
    }
}

if (! function_exists('panelUrl')) {
    /**
     * @throws Throwable
     */
    function panelUrl(Panel|string|null $panel = null): string
    {
        if (is_string($panel)) {
            $panel = filament()->getPanel($panel);
        }

        if (blank($panel)) {
            $panel = filament()->getDefaultPanel();
        }

        if (auth()->guest()) {
            return $panel->getLoginUrl() ?? url('home/login');
        }

        $stringable = str('{base}/{path}/{tenant}')->replace(['{base}', '{path}'], [url('/'), $panel->getPath()]);

        $tenant = '';

        $user = auth()->user();

        if ($panel->hasTenancy() && $user instanceof HasDefaultTenant) {
            $tenant = $user->getDefaultTenant($panel)?->getRouteKey();
        }

        return $stringable->replace('{tenant}', asString($tenant))->toString();
    }
}
