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
        Schema::create('user_support_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_support_id')->nullable();
            $table->foreign('user_support_id')->references('id')->on('user_support')->onDelete('cascade');

            $table->string('sender')->default('user');
            $table->text('message');
            $table->text('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_support_messages');
    }
};
