<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot', function (Blueprint $table) {
            $table->string('status')
                ->nullable()
                ->default('not-trained')
                ->after('height');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
