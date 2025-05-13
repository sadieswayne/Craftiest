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
        Schema::create('settings_two', function (Blueprint $table) {
            $table->id();
            $table->string('stable_diffusion_api_key')->nullable();
            $table->string('stable_diffusion_default_model')->nullable();
            $table->boolean('google_recaptcha_status')->default(false);
            $table->string('google_recaptcha_site_key')->nullable();
            $table->string('google_recaptcha_secret_key')->nullable();
            $table->string('languages')->default('en')->nullable();
            $table->string('languages_default')->default('en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_two');
    }
};
