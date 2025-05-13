<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->boolean('bolt_menu')
                ->default(false)
                ->nullable()
                ->after('extension');
            $table->string('bolt_background')->nullable()->after('bolt_menu');
            $table->string('bolt_foreground')->nullable()->after('bolt_background');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['bolt_menu', 'bolt_background', 'bolt_foreground']);
        });
    }
};
