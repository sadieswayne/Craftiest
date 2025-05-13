<?php

use App\Domains\Entity\Enums\EntityEnum;
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
        Schema::table('settings_two', function (Blueprint $table) {
            $table->string('dalle')->nullable()->default(EntityEnum::DALL_E_3->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings_two')) {
            Schema::table('settings_two', function (Blueprint $table) {
                $table->dropColumn('dalle');
            });
        }
    }
};
