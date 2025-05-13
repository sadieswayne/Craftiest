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
        Schema::create('user_openai_chat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('openai_chat_category_id')->nullable();
            $table->foreign('openai_chat_category_id')->references('id')->on('openai_chat_category')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('total_credits')->nullable();
            $table->string('total_words')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_openai_chat');
    }
};
