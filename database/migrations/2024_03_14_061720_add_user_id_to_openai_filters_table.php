<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('openai_filters', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('openai_filters', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
