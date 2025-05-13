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
        Schema::table('settings_two', function (Blueprint $table) {
            $table->string('ai_image_storage')->default('public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings_two')) {
            Schema::table('settings_two', function (Blueprint $table) {
                $table->dropColumn('ai_image_storage');
            });
        }
    }
};
