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
        Schema::create('openai_chat_category', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('role')->nullable();
            $table->string('human_name')->nullable();
            $table->string('helps_with')->nullable();
            $table->string('prompt_prefix')->nullable();
            $table->string('image')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('openai_chat_category');
    }
};
