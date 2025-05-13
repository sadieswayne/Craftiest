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
        Schema::create('custom_biling_plans', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->nullable();
            $table->string('plan_id')->nullable();
            $table->string('main_plan_price_id')->nullable();
            $table->string('custom_plan_price_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_biling_plans');
    }
};
