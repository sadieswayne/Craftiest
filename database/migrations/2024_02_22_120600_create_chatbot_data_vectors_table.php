<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_data_vectors', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chatbot_id')->nullable();
            $table->bigInteger('chatbot_data_id')->nullable();
            $table->longText('content')->nullable();
            $table->json('embedding')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_data_vectors');
    }
};
