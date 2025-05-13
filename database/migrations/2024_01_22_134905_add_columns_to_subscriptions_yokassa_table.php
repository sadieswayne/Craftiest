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
        Schema::table('subscriptions_yokassa', function (Blueprint $table) {
            $table->string('tax_rate')->nullable();
            $table->string('tax_value')->nullable();
            $table->string('coupon')->nullable();
            $table->string('total_amount')->nullable();
            $table->boolean('auto_renewal')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions_yokassa', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'tax_value', 'coupon', 'total_amount', 'auto_renewal']);
        });
    }
};
