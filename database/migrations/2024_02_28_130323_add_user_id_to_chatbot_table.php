<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_category', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->after('id');
        });
        Schema::table('chatbot', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->after('id');
        });
        Schema::table('openai_chat_category', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('chat_category', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('chatbot', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('openai_chat_category', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
