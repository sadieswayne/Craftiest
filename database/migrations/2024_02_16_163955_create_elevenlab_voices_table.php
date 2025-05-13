<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elevenlab_voices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('voice_id')->nullable();
            $table->string('path')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elevenlab_voices');
    }
};
