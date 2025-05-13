<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_openai', function (Blueprint $table) {
            $table->string('request_id')->after('id')->nullable();
            $table->string('status')->nullable()->default('COMPLETED');
        });
    }

    public function down(): void
    {
        Schema::table('user_openai', function (Blueprint $table) {
            //
        });
    }
};
