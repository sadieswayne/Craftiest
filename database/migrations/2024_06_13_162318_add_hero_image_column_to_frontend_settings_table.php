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
        Schema::table('frontend_footer_settings', function (Blueprint $table) {
            $table->string('hero_image')
                ->after('hero_button_url')
                ->default('/themes/dark/assets/landing-page/banner-img.jpg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_footer_settings', function (Blueprint $table) {
            $table->dropColumn('hero_image');
        });
    }
};
