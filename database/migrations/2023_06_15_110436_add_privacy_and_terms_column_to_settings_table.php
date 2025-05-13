<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('privacy_enable')->default(0);
            $table->boolean('privacy_enable_login')->default(0);
            $table->text('privacy_content')->nullable();
            $table->text('terms_content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'privacy_enable',
                'privacy_enable_login',
                'privacy_content',
                'terms_content',
            ]);
        });
    }
};
