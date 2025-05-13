<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('gdpr_status')->default(0);
            $table->string('gdpr_button')->default('Accept');
            $table->string('gdpr_content')->nullable()->default('This website uses cookies to improve your web experience.');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['gdpr_status', 'gdpr_button', 'gdpr_content']);
        });
    }
};
