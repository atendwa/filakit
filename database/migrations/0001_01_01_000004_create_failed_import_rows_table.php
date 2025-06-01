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
        Schema::create('failed_import_rows', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->json('data');
            $blueprint->foreignId('import_id')->constrained()->cascadeOnDelete();
            $blueprint->text('validation_error')->nullable();
            $blueprint->timestamps();
        });
    }
};
