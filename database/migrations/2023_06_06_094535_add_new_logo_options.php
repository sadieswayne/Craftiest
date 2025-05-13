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
        Schema::table('settings', function (Blueprint $table) {
            $table->text('logo_sticky')->nullable();
            $table->text('logo_sticky_path')->nullable();
            $table->text('logo_sticky_2x')->nullable();
            $table->text('logo_sticky_2x_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_sticky',
                'logo_sticky_path',
                'logo_sticky_2x',
                'logo_sticky_2x_path',
            ]);
        });
    }
};
