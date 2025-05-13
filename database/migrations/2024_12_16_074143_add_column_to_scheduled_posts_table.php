<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('scheduled_posts')) {
            Schema::table('scheduled_posts', function (Blueprint $table) {
                $table->text('content')->nullable()->after('prompt');
                $table->text('media')->nullable()->after('content');
                $table->boolean('auto_generate')->default(false)->nullable()->after('media');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('scheduled_posts')) {
            Schema::table('scheduled_posts', function (Blueprint $table) {
                $table->dropColumn(['content', 'media', 'auto_generate']);
            });
        }
    }
};
