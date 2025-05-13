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
        Schema::create('engines', static function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('status')->default(StatusEnum::ENABLED->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engines');
    }
};
