<?php

use App\Models\SettingTwo;
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
        $this->checkSD();
    }

    private function checkSD(): void
    {
        $settingTwo = SettingTwo::query()->first();
        if ($settingTwo) {
            $deprecatedEntities = ['stable-diffusion-xl-beta-v2-2-2', 'stable-diffusion-xl-1024-v0-9', 'stable-diffusion-512-v2-1'];
            $stableDiffusionDefaultModel = $settingTwo->stablediffusion_default_model;
            if (in_array($stableDiffusionDefaultModel, $deprecatedEntities, true)) {
                $settingTwo->update(['stablediffusion_default_model' => 'stable-diffusion-xl-1024-v1-0']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remove_deprecated_entities_before_migration', function (Blueprint $table) {
            //
        });
    }
};
