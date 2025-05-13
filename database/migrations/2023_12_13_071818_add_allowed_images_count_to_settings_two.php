<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings_two', function (Blueprint $table) {
            $table->boolean('daily_limit_enabled')->default(false);
            $table->integer('allowed_images_count')->default(2);
        });
    }

    public function down(): void
    {
        Schema::table('settings_two', function (Blueprint $table) {
            $table->dropColumn(['daily_limit_enabled', 'allowed_images_count']);
        });
    }
};
