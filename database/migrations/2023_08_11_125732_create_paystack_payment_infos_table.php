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
        Schema::create('paystack_payment_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('reference')->nullable();
            $table->string('trans')->nullable();
            $table->string('status')->nullable();
            $table->string('message')->nullable();
            $table->string('transaction')->nullable();
            $table->string('trxref')->nullable();
            $table->string('amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('plan_code')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('other')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paystack_payment_infos');
    }
};
