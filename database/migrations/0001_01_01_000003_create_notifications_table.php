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
        Schema::create('notifications', function (Blueprint $blueprint): void {
            $blueprint->uuid('id')->primary();
            $blueprint->string('type');
            $blueprint->morphs('notifiable');
            $blueprint->text('data');
            $blueprint->timestamp('read_at')->nullable();
            $blueprint->timestamps();
        });
    }
};
