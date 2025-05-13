<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entities', static function ($table) {
            $table->renameColumn('ai_engine', 'engine');
        });
    }

    public function down(): void
    {
        Schema::table('entities', static function ($table) {
            $table->renameColumn('engine', 'ai_engine');
        });
    }
};
