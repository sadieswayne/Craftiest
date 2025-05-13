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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('feature_image')->nullable();
            $table->string('slug')->unique();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('category')->nullable();
            $table->text('tag')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
