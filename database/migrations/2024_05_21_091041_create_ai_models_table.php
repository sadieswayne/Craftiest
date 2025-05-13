<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_models', static function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('title');
            $table->string('ai_engine')->default(\App\Domains\Engine\Enums\EngineEnum::OPEN_AI->value);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
