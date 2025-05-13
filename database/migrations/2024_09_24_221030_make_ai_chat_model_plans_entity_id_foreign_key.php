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

        Schema::table('ai_chat_model_plans', static function (Blueprint $table) {
            $table->unsignedBigInteger('entity_id')->change();
            if (! Schema::hasIndex('ai_chat_model_plans', 'ai_chat_model_plans_entity_id_foreign')) {
                $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_chat_model_plans', static function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropColumn('entity_id');
        });
    }
};
