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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('surname');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('type')->default('user');
            $table->string('password');
            $table->string('avatar')->default('assets/img/auth/default-avatar.png');
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('country')->nullable();
            $table->text('address')->nullable();
            $table->string('postal')->nullable();
            $table->boolean('status')->default('1');
            $table->integer('remaining_words')->default(0);
            $table->integer('remaining_images')->default(0);
            $table->date('last_seen')->nullable();

            $table->string('github_id')->nullable();
            $table->string('github_token')->nullable();

            $table->string('google_id')->nullable();
            $table->string('google_token')->nullable();

            $table->string('facebook_id')->nullable();
            $table->string('facebook_token')->nullable();

            $table->string('twitter_id')->nullable();
            $table->string('twitter_token')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
