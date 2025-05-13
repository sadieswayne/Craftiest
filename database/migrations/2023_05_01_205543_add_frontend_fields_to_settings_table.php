<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('frontend_pricing_section')->default(1);
            $table->boolean('frontend_custom_templates_section')->default(1);
            $table->boolean('frontend_business_partners_section')->default(1);
            $table->string('frontend_additional_url')->nullable();
            $table->string('frontend_custom_js')->nullable();
            $table->string('frontend_custom_css')->nullable();
            $table->string('frontend_footer_facebook')->nullable();
            $table->string('frontend_footer_twitter')->nullable();
            $table->string('frontend_footer_instagram')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'frontend_pricing_section',
                'frontend_custom_templates_section',
                'frontend_business_partners_section',
                'frontend_additional_url',
                'frontend_custom_js',
                'frontend_custom_css',
                'frontend_footer_facebook',
                'frontend_footer_twitter',
                'frontend_footer_instagram',
            ]);
        });
    }
};
