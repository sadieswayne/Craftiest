<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chatbot_id')->nullable();
            $table->longText('content')->nullable();
            $table->string('type')->nullable();
            $table->string('type_value')->nullable();
            $table->string('path')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_data');
    }
};
