<?php

namespace Atendwa\Filakit\Contracts;

interface HasFilamentTabs
{
    /**
     * @return array<string, string>
     */
    public static function getFilamentTabIcons(): array;

    /**
     * @return string[]
     */
    public static function getFilamentTabs(): array;

    public function getFilamentTabColumn(): string;

    public function finalSuccessStates(): string;
}
