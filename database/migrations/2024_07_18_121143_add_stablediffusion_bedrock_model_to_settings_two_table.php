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
            $table->string('stablediffusion_bedrock_model')->default('stability.stable-diffusion-xl-v1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings_two', function (Blueprint $table) {
            //
        });
    }
};
