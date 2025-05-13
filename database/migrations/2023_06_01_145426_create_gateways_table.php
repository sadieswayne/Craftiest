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
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->integer('is_active')->default(0);
            $table->string('mode')->nullable();
            $table->string('sandbox_client_id')->nullable();
            $table->string('sandbox_client_secret')->nullable();
            $table->string('sandbox_app_id')->nullable();
            $table->string('live_client_id')->nullable();
            $table->string('live_client_secret')->nullable();
            $table->string('live_app_id')->nullable();
            $table->string('payment_action')->nullable(); // PAYPAL: Can only be 'Sale', 'Authorization' or 'Order'
            $table->string('currency')->nullable();
            $table->string('currency_locale')->nullable();
            $table->string('notify_url')->nullable();
            $table->string('base_url')->nullable();
            $table->string('sandbox_url')->nullable();
            $table->string('locale')->nullable();
            $table->string('validate_ssl')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->string('logger')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
