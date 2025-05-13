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
        Schema::table('plans', static function (Blueprint $table) {
            $table->dropColumn(['total_words', 'total_images', 'display_imag_count', 'display_word_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', static function (Blueprint $table) {
            $table->integer('total_words')->default(0);
            $table->integer('total_images')->default(0);
            $table->boolean('display_imag_count')->default(1);
            $table->boolean('display_word_count')->default(1);
        });
    }
};
