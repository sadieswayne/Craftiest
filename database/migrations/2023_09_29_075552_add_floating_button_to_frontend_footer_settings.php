<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frontend_footer_settings', function (Blueprint $table) {
            $table->string('floating_button_small_text')->nullable();
            $table->string('floating_button_bold_text')->nullable();
            $table->string('floating_button_link')->nullable();
            $table->boolean('floating_button_active')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('frontend_footer_settings', function (Blueprint $table) {
            $table->dropColumn([
                'floating_button_small_text',
                'floating_button_bold_text',
                'floating_button_link',
                'floating_button_active',
            ]);
        });
    }
};
