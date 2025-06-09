<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Contracts;

use Atendwa\Filakit\Widgets\Stat;
use Illuminate\Database\Eloquent\Model;

interface HasNavigationLink
{
    public static function panelAccessPermission(): string;

    public static function canAccess(?Model $model = null): bool;

    public function toNavigationItem(): ?Stat;

    /**
     * @return array<string, string>
     */
    public function featureData(): array;

    public function featureName(): string;

    public function getId(): string;
}
