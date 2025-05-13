<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user_openai_chat', 'website_url')) {
            return;
        }

        Schema::table('user_openai_chat', function (Blueprint $table) {
            $table->string('website_url')->nullable();
        });
    }

    public function down(): void {}
};
