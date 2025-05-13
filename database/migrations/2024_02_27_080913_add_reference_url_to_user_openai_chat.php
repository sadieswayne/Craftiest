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
            $table->string('reference_url')->default('');
            $table->string('doc_name')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_openai_chat', function (Blueprint $table) {
            $table->dropColumn(['reference_url', 'doc_name']);
        });
    }
};
