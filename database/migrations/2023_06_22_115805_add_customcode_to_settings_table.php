<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('frontend_code_before_head')->nullable();
            $table->text('frontend_code_before_body')->nullable();
            $table->text('dashboard_code_before_head')->nullable();
            $table->text('dashboard_code_before_body')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'frontend_code_before_head',
                'frontend_code_before_body',
                'dashboard_code_before_head',
                'dashboard_code_before_body',
            ]);
        });
    }
};
