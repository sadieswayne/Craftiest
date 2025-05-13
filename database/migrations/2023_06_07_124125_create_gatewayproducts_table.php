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
        Schema::create('gatewayproducts', function (Blueprint $table) {
            $table->id();
            $table->integer('plan_id')->default(0);
            $table->string('plan_name')->nullable();
            $table->string('gateway_code')->nullable();
            $table->string('gateway_title')->nullable();
            $table->string('product_id')->nullable();
            $table->string('price_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gatewayproducts');
    }
};
