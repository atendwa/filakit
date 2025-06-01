<?php

declare(strict_types=1);

use Atendwa\Support\Concerns\Support\InferMigrationDownMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    use InferMigrationDownMethod;

    public function up(): void
    {
        Schema::create('exports', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->timestamp('completed_at')->nullable();
            $blueprint->string('file_disk');
            $blueprint->string('file_name')->nullable();
            $blueprint->string('exporter');
            $blueprint->unsignedInteger('processed_rows')->default(0);
            $blueprint->unsignedInteger('total_rows');
            $blueprint->unsignedInteger('successful_rows')->default(0);
            $blueprint->unsignedInteger('user_id');
            $blueprint->timestamps();
        });
    }
};
