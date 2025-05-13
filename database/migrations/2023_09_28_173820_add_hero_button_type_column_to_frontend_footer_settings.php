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
            // button type [1=website link, 2=video link]
            $table->integer('hero_button_type')->default(1)->after('hero_button_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_footer_settings', function (Blueprint $table) {
            $table->dropColumn('hero_button_type');
        });
    }
};
