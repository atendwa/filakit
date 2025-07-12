<?php

declare(strict_types=1);

namespace Atendwa\Filakit;

use Exception;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;
use Illuminate\View\Middleware\ShareErrorsFromSession;

abstract class PanelProvider extends ServiceProvider
{
    protected bool $isDefault = false;

    protected ?string $directory = null;

    protected bool $hasTenancy = true;

    protected ?string $name = null;

    protected ?string $path = null;

    /**
     * @var array<class-string>
     */
    protected array $pages = [Dashboard::class];

    public function panel(Panel $panel): Panel
    {
        return $panel;
    }

    /**
     * @throws Exception
     */
    public function register(): void
    {
        [$namespace, $dir] = $this->paths();

        Filament::registerPanel(function () use ($dir, $namespace) {
            $panel = Panel::make()->id($this->getId())->brandLogo(asset(asString(config('filakit.theme.logo.light'))))
                ->discoverResources(base_path($dir->append('Resources')->toString()), $namespace . '\\Resources')
                ->discoverClusters(base_path($dir->append('Clusters')->toString()), $namespace . '\\Clusters')
                ->discoverPages(base_path($dir->append('Pages')->toString()), $namespace . '\\Pages')->spa()
                ->favicon(asset(asString(config('filakit.theme.favicon'))))->authMiddleware([Authenticate::class])
//                ->discoverWidgets(base_path($dir->append('Widgets')->toString()), $namespace . '\\Widgets')
                ->unsavedChangesAlerts(fn () => app()->isProduction())->sidebarCollapsibleOnDesktop()->topNavigation()
                ->darkModeBrandLogo(asset(asString(config('filakit.theme.logo.dark'))))->path($this->getPath())
                ->databaseNotificationsPolling(null)->pages($this->getPages())->default($this->isDefault)
                ->viteTheme(asString(config('filakit.theme.css')))->databaseNotifications()->widgets([])
                ->login(asString(config('filakit.login_page')))->maxContentWidth('full')
                ->globalSearch()->font(asString(config('filakit.theme.font')))->databaseTransactions()
                ->brandLogoHeight(fn (): string => [true => '2.75rem', false => '5rem'][auth()->check()])
                ->middleware([
                    DispatchServingFilamentEvent::class, AddQueuedCookiesToResponse::class, AuthenticateSession::class,
                    ShareErrorsFromSession::class, DisableBladeIconComponents::class, SubstituteBindings::class,
                    VerifyCsrfToken::class, EncryptCookies::class, StartSession::class,
                ])->spaUrlExceptions(function (): array {
                    $exceptions = config('filakit.spa_url_exceptions');

                    return match (is_array($exceptions)) {
                        true => $exceptions,
                        false => [],
                    };
                })->colors(function (): array {
                    $colours = config('filakit.theme.colours');

                    return match (is_array($colours)) {
                        true => $colours,
                        false => [],
                    };
                });

            $panel = $panel->plugins($this->getPlugins($panel));

            return $this->panel($panel);
        });
    }

    public static function canAccess(string $id): bool
    {
        $panel = filament()->getPanel($id);

        $allow = false;

        collect([$panel->getPages(), $panel->getResources()])->collapse()->values()
            ->each(function (string $asset) use (&$allow): void {
                if ($allow) {
                    return;
                }

                $asset = app($asset);

                if ($asset instanceof Resource || $asset instanceof Page) {
                    $allow = $asset::canAccess();
                }
            });

        return boolval($allow);
    }

    public function getId(): string
    {
        return str($this->getName())->kebab()->lower()->toString();
    }

    /**
     * @return array<class-string>
     */
    protected function getPages(): array
    {
        return $this->pages;
    }

    protected function getName(): string
    {
        return $this->name ?? str(static::class)->afterLast('\\')
            ->before('PanelProvider')->headline()->toString();
    }

    protected function getPath(): string
    {
        return str($this->path ?? $this->getName())->kebab()->lower()->toString();
    }

    protected function getDirectoryName(?string $dir = null): ?string
    {
        if (filled($this->directory)) {
            return $this->directory;
        }

        $name = str(static::class)->between('Providers\\', 'PanelProvider')->remove('Filament\\')->toString();

        return match (File::exists(base_path($dir ?? $this->getDirectory()->toString()) . $name)) {
            true => $name,
            false => null,
        };
    }

    /**
     * @return array<Plugin>
     */
    protected function getPlugins(Panel $panel): array
    {
        return array_merge($this->defaultPlugins(), $this->plugins($panel));
    }

    /**
     * @return array<Plugin>
     */
    protected function plugins(Panel $panel): array
    {
        return $panel->getPlugins();
    }

    /**
     * @return array<Plugin>
     */
    protected function defaultPlugins(): array
    {
        $plugins = config('filakit.plugins');

        return collect(match (is_array($plugins)) {
            true => $plugins,
            false => [],
        })->filter(fn ($plugin): bool => $plugin instanceof Plugin)->all();
    }

    /**
     * @return array<Stringable>
     */
    private function paths(): array
    {
        $stringable = $this->getDirectory();

        $namespace = $stringable->replace('/', '\\')->ucfirst()->beforeLast('\\');
        $directory = $this->getDirectoryName($stringable->toString());

        return match (filled($directory)) {
            true => [$namespace->append('\\' . $directory), $stringable->append($directory . '/')],
            false => [$namespace, $stringable],
        };
    }

    private function getDirectory(): Stringable
    {
        return str(static::class)->lcfirst()->replace('\\', '/')
            ->before('Providers')->append('Filament/');
    }
}
