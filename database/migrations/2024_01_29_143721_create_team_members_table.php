<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role')->default('member');
            $table->string('email')->nullable();
            $table->string('status')->default('waiting')->nullable();
            $table->boolean('allow_unlimited_credits')->default(true);
            $table->integer('remaining_images')->nullable();
            $table->integer('remaining_words')->nullable();
            $table->integer('used_image_credit')->default(0);
            $table->integer('used_word_credit')->default(0);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
