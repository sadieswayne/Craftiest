<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('role')->nullable();
            $table->string('model')->nullable();
            $table->string('first_message')->nullable();
            $table->text('instructions')->nullable();
            $table->string('image')->nullable();
            $table->string('color')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot');
    }
};
