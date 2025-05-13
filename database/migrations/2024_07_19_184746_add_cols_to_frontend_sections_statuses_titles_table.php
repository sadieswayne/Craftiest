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
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->text('marquee_items')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->dropColumn('marquee_items');
        });
    }
};
