<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->string('advanced_features_section_title')->nullable();
            $table->text('advanced_features_section_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            //
        });
    }
};
