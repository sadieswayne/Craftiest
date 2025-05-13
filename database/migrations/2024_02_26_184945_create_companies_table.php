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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('industry')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('tagline')->nullable();
            $table->string('logo')->nullable();
            $table->string('brand_color')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Add a nullable foreign key to link to the User model
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
