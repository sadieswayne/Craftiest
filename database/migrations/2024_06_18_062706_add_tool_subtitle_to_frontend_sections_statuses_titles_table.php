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
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->string('tools_subtitle')->default('Unleash the Power of AI')->after('tools_title');
            $table->string('custom_templates_learn_more_link')->default('Discover MagicAI')->after('custom_templates_title');
            $table->string('custom_templates_learn_more_link_url')->default('#templates')->after('custom_templates_title');
            $table->string('features_subtitle')->default('Unleash the Power of AI')->after('features_title');
            $table->string('how_it_works_subtitle')->default('Unleash the Power of AI')->after('how_it_works_title');
            $table->string('how_it_works_description')->default('To create content quickly and effectively, <strong>here are the steps you can follow:</strong>')->after('how_it_works_title');
            $table->string('how_it_works_link')->default('#')->after('how_it_works_title');
            $table->string('how_it_works_link_label')->default('Learn More')->after('how_it_works_title');
            $table->string('pricing_subtitle')->default('Unleash the Power of AI')->after('pricing_title');
            $table->string('testimonials_description')->default('Content and <strong>kickstart your earnings</strong> in minutes  kickstart your earnings in minutes')->after('testimonials_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->dropColumn('tools_subtitle');
            $table->dropColumn('custom_templates_learn_more_link');
            $table->dropColumn('custom_templates_learn_more_link_url');
            $table->dropColumn('features_subtitle');
            $table->dropColumn('how_it_works_subtitle');
            $table->dropColumn('how_it_works_description');
            $table->dropColumn('how_it_works_link');
            $table->dropColumn('how_it_works_link_label');
            $table->dropColumn('pricing_subtitle');
            $table->dropColumn('testimonials_description');
        });
    }
};
