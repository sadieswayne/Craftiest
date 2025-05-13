<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity', function (Blueprint $table) {
            $table->dropColumn('activity');
            $table->string('activity_title')->nullable();
            $table->string('activity_type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('activity', function (Blueprint $table) {
            if (Schema::hasColumn('activity', 'activity_title')) {
                $table->dropColumn('activity_title');
            }
        });

        Schema::table('activity', function (Blueprint $table) {
            if (Schema::hasColumn('activity', 'activity_type')) {
                $table->dropColumn('activity_type');
            }
        });
    }
};
