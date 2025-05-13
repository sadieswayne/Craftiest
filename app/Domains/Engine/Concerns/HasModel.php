<?php

declare(strict_types=1);

namespace App\Domains\Engine\Concerns;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Models\Engine;
use App\Domains\Entity\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait HasModel
{
    use HasStatus;

    public function model(): Builder|Model|null
    {
        return $this->getEngines()?->firstWhere('key.value', value: $this->name());
    }

    private function getEngines(): ?Collection
    {
        return Cache::remember('engines', 60, static function () {
            $validEngines = collect(EngineEnum::cases())->pluck('value');
            Engine::whereNotIn('key', $validEngines)->delete();

            return Engine::whereIn('key', $validEngines)->get();
        });
    }
}
