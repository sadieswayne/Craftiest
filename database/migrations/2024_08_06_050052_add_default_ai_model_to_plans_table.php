<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('default_ai_model')->default('gpt-3.5-turbo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
};
