<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tokens', static function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite' && Schema::hasColumn('tokens', 'ai_model_id')) {
                $table->dropForeign(['ai_model_id']);
            }
        });

        Schema::table('tokens', static function (Blueprint $table) {
            if (Schema::hasColumn('tokens', 'ai_model_id')) {
                $table->renameColumn('ai_model_id', 'entity_id');
            }
        });

        Schema::table('tokens', static function (Blueprint $table) {
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tokens', static function (Blueprint $table) {
            if (Schema::hasColumn('tokens', 'entity_id')) {
                $table->dropForeign(['entity_id']);
            }
        });

        Schema::table('tokens', static function (Blueprint $table) {
            if (Schema::hasColumn('tokens', 'entity_id')) {
                $table->renameColumn('entity_id', 'ai_model_id');
            }
        });

        Schema::table('tokens', static function (Blueprint $table) {
            $table->foreign('ai_model_id')->references('id')->on('entities')->onDelete('cascade');
        });
    }
};
