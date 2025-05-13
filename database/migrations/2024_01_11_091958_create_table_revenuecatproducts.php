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
        Schema::create('revenuecat_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('gatewayproduct_id')->nullable();
            $table->string('entitlement_id')->nullable();
            $table->string('package_id')->nullable();
            $table->string('google_id')->nullable();
            $table->string('apple_id')->nullable();
            $table->string('amazon_id')->nullable();
            $table->timestamps();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('gatewayproduct_id')->references('id')->on('gatewayproducts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenuecat_products');
    }
};
