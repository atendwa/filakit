<?php

namespace Atendwa\Filakit\Concerns;

trait UsesFilamentTabs
{
    /**
     * @return array<string, string>
     */
    public static function getFilamentTabIcons(): array
    {
        return [
            'others' => 'heroicon-o-exclamation-circle',
            'all' => 'heroicon-o-rectangle-stack',
            'completed' => 'heroicon-o-sparkles',
        ];
    }

    public function getFilamentTabColumn(): string
    {
        return 'status';
    }
}
