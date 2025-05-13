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
            $table->string('hero_title_text_rotator')->nullable()->default('Generator,Chatbot,Assistant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_footer_settings', function (Blueprint $table) {
            $table->dropColumn('hero_title_text_rotator');
        });
    }
};
