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
        Schema::table('settings_two', static function (Blueprint $table) {
            if (Schema::hasColumn('settings_two', 'stablediffusion_default_model')) {
                $table->string('stablediffusion_default_model')->default(EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug())->change();
            } else {
                $table->string('stablediffusion_default_model')->default(EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug());
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings_two')) {
            Schema::table('settings_two', function (Blueprint $table) {
                $table->string('stablediffusion_default_model')->default(EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug())->change();
            });
        }
    }
};
