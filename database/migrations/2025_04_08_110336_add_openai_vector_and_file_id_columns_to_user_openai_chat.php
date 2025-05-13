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
        Schema::table('user_openai_chat', function (Blueprint $table) {
            $table->string('openai_vector_id')->nullable();
            $table->string('openai_file_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_openai_chat', function (Blueprint $table) {
            $table->dropColumn('openai_vector_id');
            $table->dropColumn('openai_file_id');
        });
    }
};
