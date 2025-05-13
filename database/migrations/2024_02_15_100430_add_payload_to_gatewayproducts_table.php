<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gatewayproducts', function (Blueprint $table) {
            $table->json('payload')->nullable()->after('price_id');
        });
    }

    public function down(): void
    {
        Schema::table('gatewayproducts', function (Blueprint $table) {
            $table->dropColumn('payload');
        });
    }
};
