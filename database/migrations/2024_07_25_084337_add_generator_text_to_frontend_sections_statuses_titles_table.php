<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->string('generators_subtitle')->nullable();
            $table->string('generators_title')->nullable();
            $table->string('generators_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            //
        });
    }
};
