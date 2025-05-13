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
        Schema::table('howitworks', function (Blueprint $table) {
            $table->string('bg_color')->nullable();
            $table->string('bg_image')->nullable();
            $table->string('text_color')->nullable();
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('howitworks', function (Blueprint $table) {
            $table->dropColumn('bg_color');
            $table->dropColumn('bg_image');
            $table->dropColumn('text_color');
            $table->dropColumn('image');
        });
    }
};
