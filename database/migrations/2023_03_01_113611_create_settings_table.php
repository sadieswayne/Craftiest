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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // INVOICE
            $table->string('invoice_currency')->nullable();
            $table->string('invoice_name')->nullable();
            $table->string('invoice_website')->nullable();
            $table->string('invoice_address')->nullable();
            $table->string('invoice_city')->nullable();
            $table->string('invoice_state')->nullable();
            $table->string('invoice_postal')->nullable();
            $table->string('invoice_country')->nullable();
            $table->string('invoice_phone')->nullable();
            $table->string('invoice_vat')->nullable();

            // PAYMENT
            $table->string('default_currency')->default('2');
            $table->string('tax_rate')->nullable();
            $table->string('stripe_active')->default(0);
            $table->string('stripe_key')->nullable();
            $table->string('stripe_secret')->nullable();
            $table->string('stripe_base_url')->default('https://api.stripe.com');

            $table->string('bank_transfer_active')->default(0);
            $table->string('bank_transfer_instructions')->nullable();
            $table->string('bank_transfer_informations')->nullable();

            // GLOBAL SETTINGS
            $table->string('site_name')->default('MagicAI');
            $table->string('site_url')->default('https://liquid-themes.com');
            $table->string('site_email')->nullable();
            $table->string('google_analytics_active')->default(0);
            $table->text('google_analytics_code')->nullable();
            $table->string('logo')->default('magicAI-logo.svg');
            $table->string('favicon')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // SOCIAL LOGIN
            $table->boolean('facebook_active')->default(0);
            $table->text('facebook_api_key')->nullable();
            $table->text('facebook_api_secret')->nullable();
            $table->text('facebook_redirect_url')->nullable();

            $table->boolean('github_active')->default(0);
            $table->text('github_api_key')->nullable();
            $table->text('github_api_secret')->nullable();
            $table->text('github_redirect_url')->nullable();

            $table->boolean('google_active')->default(0);
            $table->text('google_api_key')->nullable();
            $table->text('google_api_secret')->nullable();
            $table->text('google_redirect_url')->nullable();

            $table->boolean('twitter_active')->default(0);
            $table->text('twitter_api_key')->nullable();
            $table->text('twitter_api_secret')->nullable();
            $table->text('twitter_redirect_url')->nullable();

            // REGISTER
            $table->boolean('register_active')->default(1);
            $table->string('default_country')->default('United States');

            // SMTP
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_email')->nullable();
            $table->string('smtp_sender_name')->nullable();
            $table->string('smtp_encryption')->default('TLS');

            // OPENAI
            $table->text('openai_api_secret')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
