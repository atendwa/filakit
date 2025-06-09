<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Contracts;

use Atendwa\Filakit\Widgets\Stat;

interface HasNavigationLink
{
    public static function panelAccessPermission(): string;

    public static function canAccess(string $id): bool;

    public function toNavigationItem(): ?Stat;

    /**
     * @return array<string, string>
     */
    public function featureData(): array;

    public function featureName(): string;

    public function getId(): string;
}
