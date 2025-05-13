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
        Schema::create('openai', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('slug')->nullable();
            $table->boolean('active')->default(1);
            $table->text('questions')->nullable();
            $table->text('image')->nullable();
            $table->boolean('premium')->default(0);
            $table->string('type')->default('text');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('openai');
    }
};
