<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->integer('user_count')->default(0);
        });

        // Count existing users
        $userCount = DB::table('users')->count();

        // Set the initial value in settings table
        DB::table('settings')->updateOrInsert(
            [],
            [
                'user_count' => $userCount,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('user_count');
        });
    }
};
