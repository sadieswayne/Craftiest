<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add the new column with the default value
        Schema::table('user_openai', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('user_openai', function (Blueprint $table) {

            if (! isDBDriverSQLite()) {
                $table->dropForeign(['folder_id']);
            }

            $table->dropColumn('folder_id');
        });
    }
};
