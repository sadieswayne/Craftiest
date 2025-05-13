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
        Schema::table('privacy_terms', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
            $table->string('lang')->nullable()->change();
            $table->text('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('privacy_terms', function (Blueprint $table) {
            $table->string('type')->nullable(false)->change();
            $table->string('lang')->nullable(false)->change();
            $table->text('content')->nullable(false)->change();
        });
    }
};
