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
        Schema::create('article_wizard', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('keywords');
            $table->text('extra_keywords');
            $table->text('topic_keywords');
            $table->text('title');
            $table->text('extra_titles');
            $table->text('topic_title');
            $table->string('language')->default('');
            $table->string('tone')->default('');
            $table->string('image_style')->default('');
            $table->integer('image_count')->default(0);
            $table->text('outline');
            $table->text('extra_outlines');
            $table->text('topic_outline');
            $table->integer('current_step')->default(0);
            $table->text('result');
            $table->text('image');
            $table->text('extra_images');
            $table->text('topic_image');
            $table->integer('generated_count')->default(0);
            $table->float('creativity')->default(0.5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_wizard');
    }
};
