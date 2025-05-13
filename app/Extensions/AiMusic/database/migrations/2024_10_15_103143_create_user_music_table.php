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
        Schema::create('user_music', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('music_id')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('duration')->nullable();
            $table->string('status')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_music', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('music_id');
            $table->dropColumn('audio_url');
            $table->dropColumn('image_url');
            $table->dropColumn('video_url');
            $table->dropColumn('duration');
            $table->dropColumn('status');
            $table->dropColumn('title');
        });
    }
};
