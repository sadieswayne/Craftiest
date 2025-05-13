<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extensions', function (Blueprint $table) {
            $table->float('fake_price')->nullable()->after('price')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('extensions', function (Blueprint $table) {
            if (Schema::hasColumn('extensions', 'fake_price')) {
                $table->dropColumn('fake_price');
            }
        });
    }
};
