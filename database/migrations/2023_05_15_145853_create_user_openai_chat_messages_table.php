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
        Schema::create('user_openai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_openai_chat_id')->nullable();
            $table->foreign('user_openai_chat_id')->references('id')->on('user_openai_chat')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('input')->nullable();
            $table->text('response')->nullable();
            $table->text('output')->nullable();
            $table->text('hash')->nullable();
            $table->string('credits')->nullable();
            $table->string('words')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_openai_chat_messages');
    }
};
