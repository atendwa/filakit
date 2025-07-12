<?php

namespace Atendwa\Filakit\Utils;

class PanelPermissionResolver
{
    /**
     * @param  class-string  $panel
     */
    public function execute(string $panel): string
    {
        return str($panel)->afterLast('\\')->before('Provider')->prepend('canAccess')->toString();
    }
}
