<?php

use App\Domains\Entity\Models\Entity;
use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public static $data = [
        \App\Domains\Entity\Enums\EntityEnum::VIDEO_UPSCALER,
        \App\Domains\Entity\Enums\EntityEnum::COGVIDEOX_5B,
        \App\Domains\Entity\Enums\EntityEnum::ANIMATEDIFF_V2V,
        \App\Domains\Entity\Enums\EntityEnum::FAST_ANIMATEDIFF_TURBO,
    ];

    public function up(): void
    {
        foreach (self::$data as $model) {
            Entity::query()
                ->firstOrCreate(
                    [
                        'key' => $model->value,
                    ],
                    [
                        'engine'    => $model->engine(),
                        'key'       => $model->value,
                        'title'     => $model->label(),
                        'status'    => StatusEnum::ENABLED->value,
                    ]
                );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
