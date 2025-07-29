<?php

namespace Atendwa\Filakit\Concerns;

trait InferActiveNavigationIcon
{
    public static function getActiveNavigationIcon(): ?string
    {
        if (isset(self::$activeNavigationIcon)) {
            return self::$activeNavigationIcon;
        }

        if (filled(self::getNavigationIcon())) {
            return str(self::getNavigationIcon())->replace('-o-', '-s-')->toString();
        }

        return null;
    }
}
