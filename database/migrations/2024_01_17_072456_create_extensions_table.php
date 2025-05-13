<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable();
            $table->string('name')->nullable();
            $table->float('review')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('badge')->nullable();
            $table->string('zip_url')->nullable();
            $table->string('price_id')->nullable();
            $table->boolean('installed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extensions');
    }
};
