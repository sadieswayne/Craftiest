<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('affiliate_minimum_withdrawal')->default(10);
            $table->string('affiliate_commission_percentage')->default(10);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['affiliate_minimum_withdrawal', 'affiliate_commission_percentage']);
        });
    }
};
