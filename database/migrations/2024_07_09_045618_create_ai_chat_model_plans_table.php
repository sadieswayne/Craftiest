<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chat_model_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('plan_id');
            $table->integer('ai_model_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_model_plans');
    }
};
