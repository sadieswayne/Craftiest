<?php

use Carbon\Carbon;
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
        Schema::create('usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total_user_count')->default(0);
            $table->unsignedInteger('this_week_user_count')->default(0);
            $table->unsignedInteger('last_week_user_count')->default(0);

            $table->unsignedInteger('total_word_count')->default(0);
            $table->unsignedInteger('this_week_word_count')->default(0);
            $table->unsignedInteger('last_week_word_count')->default(0);

            $table->unsignedInteger('total_image_count')->default(0);
            $table->unsignedInteger('this_week_image_count')->default(0);
            $table->unsignedInteger('last_week_image_count')->default(0);

            $table->unsignedInteger('total_sales')->default(0);
            $table->unsignedInteger('this_week_sales')->default(0);
            $table->unsignedInteger('last_week_sales')->default(0);

            $table->timestamps();
        });

        // Count existing users
        $totaluserCount = DB::table('users')->count();
        $totalWordCount = DB::table('user_openai')->where('credits', '!=', 1)->sum('credits');
        $totalImageCount = DB::table('user_openai')->where('credits', '=', 1)->sum('credits');
        $totalSalesCount = DB::table('user_orders')->sum('price');

        // Count users created this week
        $thisWeekUserCount = DB::table('users')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $thisWeekWordCount = DB::table('user_openai')->where('credits', '!=', 1)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('credits');
        $thisWeekImageCount = DB::table('user_openai')->where('credits', '=', 1)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('credits');
        $thisWeekSales = DB::table('user_orders')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('price');

        // Count users created last week
        $lastWeekUserCount = DB::table('users')->whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()])->count();
        $lastWeekWordCount = DB::table('user_openai')->where('credits', '!=', 1)->whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()])->sum('credits');
        $lastWeekImageCount = DB::table('user_openai')->where('credits', '=', 1)->whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()])->sum('credits');
        $lastWeekSales = DB::table('user_orders')->whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()])->sum('price');

        // Set the initial value in settings table
        DB::table('usage')->updateOrInsert(
            [],
            [
                'total_user_count'      => $totaluserCount,
                'this_week_user_count'  => $thisWeekUserCount,
                'last_week_user_count'  => $lastWeekUserCount,
                'total_word_count'      => $totalWordCount,
                'this_week_word_count'  => $thisWeekWordCount,
                'last_week_word_count'  => $lastWeekWordCount,
                'total_image_count'     => $totalImageCount,
                'this_week_image_count' => $thisWeekImageCount,
                'last_week_image_count' => $lastWeekImageCount,
                'total_sales'           => $totalSalesCount,
                'this_week_sales'       => $thisWeekSales,
                'last_week_sales'       => $lastWeekSales,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage');
    }
};
