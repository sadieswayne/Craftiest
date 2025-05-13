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
        Schema::create('frontend_generators', function (Blueprint $table) {
            $table->id();
            $table->string('menu_title')->nullable();
            $table->string('subtitle_one')->nullable();
            $table->string('subtitle_two')->nullable();
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('image')->nullable();
            $table->string('image_title')->nullable();
            $table->string('image_subtitle')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontend_generators');
    }
};
