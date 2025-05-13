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
        Schema::table('frontend_tools', function (Blueprint $table) {
            $table->string('buy_link')->default('Start Making Money');
            $table->string('buy_link_url')->default('https://codecanyon.net/item/magicai-openai-content-text-image-chat-code-generator-as-saas/45408109');
            $table->string('learn_more_link')->default('Discover MagicAI');
            $table->string('learn_more_link_url')->default('#templates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_tools', function (Blueprint $table) {
            $table->dropColumn('buy_link');
            $table->dropColumn('buy_link_url');
            $table->dropColumn('learn_more_link');
            $table->dropColumn('learn_more_link_url');
        });
    }
};
