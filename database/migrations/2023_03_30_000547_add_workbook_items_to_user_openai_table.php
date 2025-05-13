<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_openai', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('user_openai', function (Blueprint $table) {
            $table->dropColumn(['title', 'slug']);
        });
    }
};
