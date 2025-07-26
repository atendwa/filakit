<?php

declare(strict_types=1);

namespace Atendwa\Filakit;

use Atendwa\Filakit\Concerns\InferActiveNavigationIcon;

class Cluster extends \Filament\Clusters\Cluster
{
    use InferActiveNavigationIcon;
}
