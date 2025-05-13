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
        Schema::create('chatbot_history', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('ip')->nullable();
            $table->integer('user_openai_chat_id')->nullable();
            $table->integer('openai_chat_category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_history');
    }
};
