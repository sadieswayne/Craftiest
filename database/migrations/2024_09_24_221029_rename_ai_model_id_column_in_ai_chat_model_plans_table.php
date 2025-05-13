<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_chat_model_plans', static function (Blueprint $table) {
            $table->unsignedBigInteger('ai_model_id')->change();
            $table->renameColumn('ai_model_id', 'entity_id');
        });
    }

    public function down(): void
    {
        Schema::table('ai_chat_model_plans', static function (Blueprint $table) {
            if (Schema::hasColumn('ai_chat_model_plans', 'entity_id')) {
                $table->dropForeign(['entity_id']);
            }
        });

        Schema::table('ai_chat_model_plans', static function (Blueprint $table) {
            if (Schema::hasColumn('ai_chat_model_plans', 'entity_id')) {
                $table->renameColumn('entity_id', 'ai_model_id');
            }
        });
    }
};
