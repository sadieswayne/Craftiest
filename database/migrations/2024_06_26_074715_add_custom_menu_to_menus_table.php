<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'custom_menu')) {
                $table->boolean('custom_menu')->default(false);
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('menus', 'custom_menu')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('custom_menu');
            });
        }
    }
};
