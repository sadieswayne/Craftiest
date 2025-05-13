<?php

declare(strict_types=1);

use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_models', static function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('ai_models', static function (Blueprint $table) {
            $table->string('status')->default(StatusEnum::ENABLED->value);
        });
    }

    public function down(): void
    {
        Schema::table('ai_models', static function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('ai_models', static function (Blueprint $table) {
            $table->boolean('is_active')->default(false);
        });
    }
};
