<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokens', static function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(\App\Enums\AITokenType::WORD->value);
            $table->foreignId('ai_model_id')->constrained('ai_models', 'id')->cascadeOnDelete();
            $table->decimal('cost_per_token', 15, 2)->default(1.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
