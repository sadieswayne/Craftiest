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
        Schema::table('photo_studios', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->string('request_id')->nullable();
            $table->text('photo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_studios', function (Blueprint $table) {
            //
        });
    }
};
