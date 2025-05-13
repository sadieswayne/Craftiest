<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('app_settings')
            ->where('key', 'default_aw_image_engine')
            ->where('value', 'sd')
            ->update(['value' => EngineEnum::STABLE_DIFFUSION->value]);
    }

    public function down(): void {}
};
