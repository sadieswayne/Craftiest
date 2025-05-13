<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Models\Entity;
use App\Enums\AITokenType;
use Illuminate\Database\Seeder;

class TokenSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAllTokens();
    }

    private function createAllTokens(): void
    {
        $models = [];
        $entities = EntityEnum::cases();
        foreach ($entities as $entity) {
            $this->createToken(
                $entity->engine(),
                $entity->tokenType(),
                $entity->value,
            );
        }
    }

    private function createToken(EngineEnum $engine, AITokenType $type, string $key): void
    {
        $engineModel = Entity::query()
            ->where('engine', $engine)
            ->where('key', $key)
            ->firstOrFail();

        $engineModel->tokens()->firstOrCreate(
            [
                'entity_id' => $engineModel->id,
            ],
            [
                'type'           => $type,
            ]
        );
    }
}
