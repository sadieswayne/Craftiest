<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('logo_dark')->default('magicAI-logo-dark.svg');
            $table->text('logo_dashboard')->nullable();
            $table->text('logo_dashboard_dark')->nullable();
            $table->string('logo_collapsed_dark')->default('magicAI-logo-collapsed-dark.svg');
            $table->text('logo_2x')->nullable();
            $table->text('logo_dark_2x')->nullable();
            $table->text('logo_dashboard_2x')->nullable();
            $table->text('logo_dashboard_dark_2x')->nullable();
            $table->text('logo_collapsed_2x')->nullable();
            $table->text('logo_collapsed_dark_2x')->nullable();
            $table->string('logo_dark_path')->default('assets/img/logo/magicAI-logo-dark.svg');
            $table->text('logo_dashboard_path')->nullable();
            $table->text('logo_dashboard_dark_path')->nullable();
            $table->string('logo_collapsed_dark_path')->default('assets/img/logo/magicAI-logo-collapsed-dark.svg');
            $table->text('logo_2x_path')->nullable();
            $table->text('logo_dark_2x_path')->nullable();
            $table->text('logo_dashboard_2x_path')->nullable();
            $table->text('logo_dashboard_dark_2x_path')->nullable();
            $table->text('logo_collapsed_2x_path')->nullable();
            $table->text('logo_collapsed_dark_2x_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_dark',
                'logo_dashboard',
                'logo_dashboard_dark',
                'logo_collapsed_dark',
                'logo_2x',
                'logo_dark_2x',
                'logo_dashboard_2x',
                'logo_dashboard_dark_2x',
                'logo_collapsed_2x',
                'logo_collapsed_dark_2x',
                'logo_dark_path',
                'logo_dashboard_path',
                'logo_dashboard_dark_path',
                'logo_collapsed_dark_path',
                'logo_2x_path',
                'logo_dark_2x_path',
                'logo_dashboard_2x_path',
                'logo_dashboard_dark_2x_path',
                'logo_collapsed_2x_path',
                'logo_collapsed_dark_2x_path',
            ]);
        });
    }
};
