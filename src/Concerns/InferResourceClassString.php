<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Filament\Pages\Page;
use Filament\Resources\Resource;
use File;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

trait InferResourceClassString
{
    /**
     * @throws Throwable
     */
    public static function getResource(): string
    {
        $class = app(static::class);
        throw_if(! $class instanceof Page);

        if (property_exists($class, 'resource') && isset($class::$resource)) {
            $resource = app(asString($class::$resource));

            throw_if(! $resource instanceof Resource, 'Resource not found');

            return $resource::class;
        }

        $path = str(static::class);

        $dir = (string) $path->after('Resources')->explode('\\')->filter()->first();
        $namespace = mb_strtolower((string) $path->explode('\\')->first());

        $path = $path->before('Resources')->append('Resources')->replace('\\', '/');
        $path = base_path($path->replaceFirst(ucfirst($namespace), $namespace)->toString());

        throw_if(! File::exists($path), 'Resource directory not found!');

        $resource = collect(File::files($path))
            ->filter(fn (SplFileInfo $file) => str($file->getFilename())->before('.php')->is($dir, true))
            ->map(fn (SplFileInfo $file): string => get_class(asObject(inferFileClass($file->getRealPath()))))
            ->first();

        $resource = app(asString($resource));

        throw_if(! $resource instanceof Resource, 'Resource not found!');

        return $resource::class;
    }

    /**
     * @throws Throwable
     */
    protected static function fetchResource(): Resource
    {
        $resource = app(self::getResource());

        throw_if(! $resource instanceof Resource, 'Resource not found');

        return $resource;
    }
}
