<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_team_plan')->default(false)->after('type');
            $table->integer('plan_allow_seat')->nullable()->after('is_team_plan');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['is_team_plan', 'plan_allow_seat']);
        });
    }
};
