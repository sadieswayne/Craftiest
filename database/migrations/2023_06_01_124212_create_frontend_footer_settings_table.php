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
        Schema::create('frontend_footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('header_title')->default('Limited Offer');
            $table->string('header_text')->default('Sign up and receive 20% bonus discount on checkout.');

            $table->string('hero_subtitle')->default('Unleash the Power of AI');
            $table->string('hero_title')->default('Ultimate AI');
            $table->string('hero_description')->default('All-in-one platform to generate AI content and start making money in minutes.');
            $table->string('hero_scroll_text')->default('Discover MagicAI');
            $table->string('hero_button')->default('Start Making Money');
            $table->string('hero_button_url')->nullable('https://codecanyon.net/item/magicai-openai-content-text-image-chat-code-generator-as-saas/45408109');

            $table->string('footer_header')->default('Start your free trial.');
            $table->string('footer_text_small')->default('Pay once, own forever.');
            $table->string('footer_text')->default('Unlock your business potential by letting the AI work and generate money for you.');
            $table->string('footer_button_text')->default('Join our community');
            $table->string('footer_button_url')->default('https://codecanyon.net/item/magicai-openai-content-text-image-chat-code-generator-as-saas/45408109');
            $table->string('footer_copyright')->default('All images are for demo purposes.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontend_footer_settings');
    }
};
