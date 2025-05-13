<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('ai_models', 'entities');
    }

    public function down(): void
    {
        Schema::rename('entities', 'ai_models');
    }
};
