<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_models', function (Blueprint $table) {
            $table->string('selected_title')->nullable();
            $table->boolean('is_selected')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('ai_models', function (Blueprint $table) {
            $table->dropColumn('is_selected');
            $table->dropColumn('selected_title');
        });
    }
};
