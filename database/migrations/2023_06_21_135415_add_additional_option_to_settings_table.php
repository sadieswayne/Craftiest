<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('feature_ai_voiceover')->default(true)->nullable();
            $table->text('gcs_file')->nullable();
            $table->text('gcs_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'feature_ai_voiceover',
                'gcs_file',
                'gcs_name',
            ]);
        });
    }
};
