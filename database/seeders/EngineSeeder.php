<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Models\Engine;
use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;

class EngineSeeder extends Seeder
{
    public function run(): void
    {
        $this->createEngines();
    }

    private function createEngines(): void
    {
        foreach (EngineEnum::cases() as $model) {
            Engine::query()
                ->firstOrCreate(
                    [
                        'key' => $model->value,
                    ],
                    [
                        'status'    => StatusEnum::ENABLED,
                    ]
                );
        }
    }
}
