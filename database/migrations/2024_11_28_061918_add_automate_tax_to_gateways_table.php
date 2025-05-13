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
        if (Schema::hasColumn('gateways', 'automate_tax')) {
            return;
        }

        Schema::table('gateways', function (Blueprint $table) {
            $table->boolean('automate_tax')->default(false)->after('tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn('automate_tax');
        });
    }
};
