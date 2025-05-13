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
        Schema::create('webhookhistory', function (Blueprint $table) {
            $table->id();
            $table->string('gatewaycode')->nullable();
            $table->string('webhook_id')->nullable();
            $table->string('create_time')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('event_type')->nullable();
            $table->string('summary')->nullable();
            $table->string('resource_id')->nullable();
            $table->string('resource_state')->nullable();
            $table->string('parent_payment')->nullable();
            $table->string('amount_total')->nullable();
            $table->string('amount_currency')->nullable();
            $table->text('incoming_json')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhookhistory');
    }
};
