<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', static function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->string('domain');
            $table->uuid('app_key');
            $table->foreignId('chatbot_id')->index()->constrained('chatbot')->onUpdate('NO ACTION')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['chatbot_id', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
