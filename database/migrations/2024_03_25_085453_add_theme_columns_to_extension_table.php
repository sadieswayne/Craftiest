<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('extensions', function (Blueprint $table) {
            $table->boolean('is_theme')->default(false)->after('licensed');
            $table->enum('theme_type', ['All', 'Frontend', 'Dashboard'])->default('All')->after('is_theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extensions', function (Blueprint $table) {
            if (Schema::hasColumn('extensions', 'theme_type')) {
                $table->dropColumn(['is_theme', 'theme_type']);
            }
        });
    }
};
