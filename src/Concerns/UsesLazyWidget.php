<?php

namespace Atendwa\Filakit\Concerns;

trait UsesLazyWidget
{
    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;
}
