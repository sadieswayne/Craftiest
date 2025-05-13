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
        Schema::table('extensions', function (Blueprint $table) {
            $table->string('image_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extensions', function (Blueprint $table) {
            if (Schema::hasColumn('extensions', 'image_url')) {
                $table->dropColumn('image_url');
            }
        });
    }
};
