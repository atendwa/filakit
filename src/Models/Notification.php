<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Models;

use Atendwa\Support\Concerns\Models\Prunable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use Prunable;

    public string $icon = 'heroicon-o-bell';

    protected function casts(): array
    {
        return [
            'type' => 'string',
            'notifiable_id' => 'integer',
            'notifiable_type' => 'string',
            'data' => 'array',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected function retentionMonths(): int
    {
        return asInteger(config('filakit.default_retention_months'));
    }
}
