<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('type', Roles::ADMIN->value)
            ->update(['type' => 'super_admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('type', 'super_admin')
            ->update(['type' => Roles::ADMIN->value]);
    }
};
