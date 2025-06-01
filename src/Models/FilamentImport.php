<?php

declare(strict_types=1);

namespace Atendwa\Filakit\Models;

use Atendwa\Support\Concerns\Models\Prunable;
use Illuminate\Database\Eloquent\Model;

class FilamentImport extends Model
{
    use Prunable;

    public string $icon = 'heroicon-o-arrow-down-on-square-stack';

    protected $table = 'exports';

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'file_name' => 'string',
            'importer' => 'string',
            'file_disk' => 'string',
            'completed_at' => 'datetime',
            'successful_rows' => 'integer',
            'processed_rows' => 'integer',
            'total_rows' => 'integer',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    protected function retentionMonths(): int
    {
        return asInteger(config('filakit.default_retention_months'));
    }
}
