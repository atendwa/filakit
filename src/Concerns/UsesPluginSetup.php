<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Concerns;

use Atendwa\Filakit\Panel;
use Closure;
use Filament\Contracts\Plugin;
use Filament\FilamentManager;

trait UsesPluginSetup
{
    protected ?bool $authorised = null;

    public function getId(): string
    {
        $class = static::class;

        return str($class)->before('\\')->append(class_basename($class))->before('Plugin')->kebab()->toString();
    }

    public static function make(): self
    {
        return new self();
    }

    public function authorise(Closure|bool $value = true): self
    {
        $this->authorised = (bool) ($value instanceof Closure ? $value() : $value);

        return $this;
    }

    public static function get(): Plugin|FilamentManager
    {
        return filament((new self())->getId());
    }

    public function authorised(): ?bool
    {
        return $this->authorised;
    }

    public function boot(Panel|\Filament\Panel $panel): void
    {
        $panel->topNavigation($panel->hasTopNavigation());
    }
}
