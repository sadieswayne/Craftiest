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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('feature_ai_writer')->default(true);
            $table->boolean('feature_ai_image')->default(true);
            $table->boolean('feature_ai_chat')->default(true);
            $table->boolean('feature_ai_code')->default(true);
            $table->boolean('feature_ai_speech_to_text')->default(true);
            $table->boolean('feature_affilates')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'feature_ai_writer',
                'feature_ai_image',
                'feature_ai_chat',
                'feature_ai_code',
                'feature_ai_speech_to_text',
                'feature_affilates',
            ]);
        });
    }
};
