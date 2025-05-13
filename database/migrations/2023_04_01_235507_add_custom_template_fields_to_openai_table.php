<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('openai', function (Blueprint $table) {
            $table->text('prompt')->nullable();
            $table->boolean('custom_template')->default(0);
            $table->boolean('tone_of_voice')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('openai', function (Blueprint $table) {
            $table->dropColumn([
                'prompt',
                'custom_template',
                'tone_of_voice',
            ]);
        });
    }
};
