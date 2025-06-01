<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Models;

use Atendwa\Support\Concerns\Models\Prunable;
use Illuminate\Database\Eloquent\Model;

class FailedImportRow extends Model
{
    use Prunable;

    public string $icon = 'heroicon-o-exclamation-triangle';

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'data' => 'array',
            'import_id' => 'integer',
            'validation_error' => 'string',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    protected function retentionMonths(): int
    {
        return asInteger(config('filakit.default_retention_months'));
    }
}
