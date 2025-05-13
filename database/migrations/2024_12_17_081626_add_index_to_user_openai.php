<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasIndex('user_openai', 'user_openai_user_id_updated_at_index')) {
            DB::statement('CREATE INDEX user_openai_user_id_updated_at_index ON user_openai (user_id ASC, updated_at DESC)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasIndex('user_openai', 'user_openai_user_id_updated_at_index')) {
            DB::statement('DROP INDEX user_openai_user_id_updated_at_index ON user_openai');
        }
    }
};
