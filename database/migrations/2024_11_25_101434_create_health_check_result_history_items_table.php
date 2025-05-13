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
        if (! Schema::hasTable('health_check_result_history_items')) {
            Schema::create('health_check_result_history_items', static function (Blueprint $table) {
                $table->id();
                $table->string('check_name');
                $table->string('check_label');
                $table->string('status');
                $table->text('notification_message');
                $table->string('short_summary')->nullable();
                $table->json('meta');
                $table->timestamp('ended_at');
                $table->char('batch', 36);
                $table->index('created_at');
                $table->index('batch');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_check_result_history_items');
    }
};
