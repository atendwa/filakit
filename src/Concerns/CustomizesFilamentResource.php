<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Filakit\Contracts\ModelHasIcon;
use Atendwa\Foundation\Scopes\IsActiveScope;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

trait CustomizesFilamentResource
{
    use CustomizesResourceTable;
    use HandlesPageAuthorization;

    public static function getRecordTitleAttribute(): ?string
    {
        return self::$recordTitleAttribute ?? self::$defaultRecordTitleAttribute ?? null;
    }

    /**
     * @throws Throwable
     */
    public static function getNavigationIcon(): string
    {
        return self::$navigationIcon ?? self::overrideIcon();
    }

    /**
     * @throws Throwable
     */
    public static function getActiveNavigationIcon(): string
    {
        return self::$activeNavigationIcon ?? self::$navigationIcon ?? self::overrideIcon(true);
    }

    /**
     * @throws Throwable
     */
    public static function overrideIcon(bool $active = false): string
    {
        $model = app(self::getModel());

        $icon = match ($model instanceof ModelHasIcon) {
            true => $model->getIcon(),
            false => null,
        };

        $icon = str(asString($icon, 'heroicon-o-rectangle-stack'));

        return match ($active) {
            true => $icon->replace('-o-', '-s-')->toString(),
            false => $icon->toString(),
        };
    }

    /**
     * @return Builder<Model>
     */
    public static function getEloquentQuery(): Builder
    {
        return self::baseQuery();
    }

    /**
     * @return Builder<Model>
     */
    public static function baseQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes(self::scopes());
    }

    public static function getRelations(): array
    {
        return self::$relations;
    }

    //
    //    /**
    //     * @return array<string>
    //     */
    //    public static function getRelations(): array
    //    {
    //        try {
    //            $path = str(static::class);
    //            $namespace = mb_strtolower((string) $path->explode('\\')->first());
    //            $path = $path->append('\RelationManagers')->replace('\\', '/');
    //            $path = base_path($path->replaceFirst(ucfirst($namespace), $namespace)->toString());
    //
    //            return collect(File::files($path))->map(fn (SplFileInfo $file) => app(inferFileClass($file)))
    //                ->filter(fn ($class): bool => $class instanceof RelationManager)->map(fn ($class) => $class::class)
    //                ->all();
    //        } catch (Throwable) {
    //            return [];
    //        }
    //    }

    /**
     * @return array<string, PageRegistration>
     *
     * @throws Throwable
     */
    public static function getPages(): array
    {
        $path = str(static::class);
        $namespace = mb_strtolower((string) $path->explode('\\')->first());
        $path = $path->append('\Pages')->replace('\\', '/');
        $path = base_path($path->replaceFirst(ucfirst($namespace), $namespace)->toString());

        throw_if(! File::exists($path), 'Pages dir not found!');

        $pages = [];

        collect(File::files($path))
            ->map(fn (SplFileInfo $file): array => self::routeMap(app(inferFileClass($file))))
            ->collapse()->filter(fn ($item): bool => $item instanceof PageRegistration)
            ->each(function (PageRegistration $pageRegistration, string $key) use (&$pages): void {
                $pages[$key] = $pageRegistration;
            });

        return $pages;
    }

    /**
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public static function isScopedToTenant(): bool
    {
        $method = str(filament()->getTenantModel() ?? 'tenant')->afterLast('\\')->lower()->toString();

        return method_exists(asObject(self::getModel()), $method);
    }

    /**
     * @throws Throwable
     */
    public static function getModel(): string
    {
        if (filled(self::$model)) {
            return self::$model;
        }

        $stringable = str(static::class);
        $namespace = $stringable->before('\\Filament')->append('\\Models\\')->toString();

        $model = str('{namespace}{name}')->replace('{namespace}', $namespace)
            ->replace('{name}', $stringable->between('\\Resources\\', 'Resource')->toString())
            ->toString();

        throw_if(! class_exists($model), 'Model not found for: ' . $stringable);

        return $model;
    }

    /**
     * @return array<class-string>
     */
    protected static function scopes(): array
    {
        return [SoftDeletingScope::class, IsActiveScope::class];
    }

    /**
     * @return array<string, PageRegistration>
     */
    private static function routeMap(mixed $page): array
    {
        return match (true) {
            $page instanceof ViewRecord => ['view' => $page::route('/{record}/view')],
            $page instanceof EditRecord => ['edit' => $page::route('/{record}/edit')],
            $page instanceof CreateRecord => ['create' => $page::route('/create')],
            $page instanceof ListRecords => ['index' => $page::route('/')],
            default => [],
        };
    }
}
