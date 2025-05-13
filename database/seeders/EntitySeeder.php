<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Models\Entity;
use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        $this->createEntities();
    }

    private function createEntities(): void
    {
        foreach (EntityEnum::cases() as $model) {
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
}
