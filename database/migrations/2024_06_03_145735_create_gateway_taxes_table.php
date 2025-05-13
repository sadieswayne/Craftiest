<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateway_taxes', function (Blueprint $table) {
            $table->id();
            $table->integer('gateway_id')->nullable();
            $table->string('country_code')->nullable();
            $table->decimal('tax', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateway_taxes');
    }
};
