<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('openai_default_model')->default('gpt-3.5-turbo');
            $table->string('openai_default_language')->default('en-US');
            $table->string('openai_default_tone_of_voice')->default('professional');
            $table->string('openai_default_creativity')->default('0.75');
            $table->string('openai_max_input_length')->default('300');
            $table->string('openai_max_output_length')->default('200');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'openai_default_model',
                'openai_default_language',
                'openai_default_tone_of_voice',
                'openai_default_creativity',
                'openai_max_input_length',
                'openai_max_output_length',
            ]);
        });
    }
};
