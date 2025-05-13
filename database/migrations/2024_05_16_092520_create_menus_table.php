<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('key')->unique();
            $table->string('route')->nullable();
            $table->string('route_slug')->nullable();
            $table->string('label')->nullable();
            $table->string('icon')->nullable();
            $table->text('svg')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->longText('params')->nullable();
            $table->string('type')->nullable();
            $table->string('extension')->nullable();
            $table->boolean('letter_icon')->default(false)->nullable();
            $table->string('letter_icon_bg')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
