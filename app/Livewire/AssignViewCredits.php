<?php

namespace App\Livewire;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class AssignViewCredits extends Component
{
    public array $entities = [];

    public array $enabledAiEngines = [];

    public array $costs = [];

    public array $entityUnitPrices = [];

    public array $totals = [];

    public $plan = null;

    public function mount(array $entities = [], $plan = null): void
    {
        $this->entities = $entities;

        $this->plan = $plan;

        $this->enabledAiEngines = EngineEnum::whereHasEnabledModels();

        $this->costs = $this->setAllCost($entities);

        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->totals['costs'] = 0;

        $total = 0;
        foreach ($this->costs as $groupKey => $groupCosts) {

            $groupCost = array_map(function ($cost) {
                return $cost['credit'];
            }, $groupCosts);

            $groupTotal = array_sum($groupCost);

            $this->totals['engine'][$groupKey] = [
                'enum'  => EngineEnum::fromSlug($groupKey),
                'total' => $groupTotal,
            ];
            $total += $groupTotal;

            $this->totals['costs'] = $total;
        }

    }

    private function setAllCost(array $entities): array
    {

        foreach ($entities as $groupKey => $groupEntities) {
            foreach ($groupEntities as $key => $entity) {
                $unitPrice = EntityEnum::fromSlug($key)?->unitPrice();

                $unitPrice = is_float($unitPrice) ? $unitPrice : 0.00;

                $credit = $entity['credit'] * $unitPrice;

                $this->entityUnitPrices[$groupKey][$key] = $unitPrice;

                if (isset($entity['isUnlimited']) && $entity['isUnlimited']) {
                    $credit = 0;
                }

                $costs[$groupKey][$key]['credit'] = $credit;
                $costs[$groupKey][$key]['isUnlimited'] = $entity['isUnlimited'] ?? false;
            }
        }

        return $costs;
    }

    public function rules(): array
    {
        return EngineEnum::rules('entities.', ['sometimes|numeric|min:0', 'sometimes|boolean']);
    }

    public function messages(): array
    {
        return collect($this->enabledAiEngines)->flatMap(function (EngineEnum $engine) {
            return collect($engine->models())->mapWithKeys(function (EntityEnum $model) use ($engine) {
                return [
                    "entities.{$engine->slug()}.{$model->slug()}.credit.numeric"      => str_replace(':aiModel', $model->value, 'The :aiModel credit must be a valid number.'),
                    "entities.{$engine->slug()}.{$model->slug()}.credit.min"          => str_replace(':aiModel', $model->value, 'The :aiModel credit must be at least :min.'),
                    "entities.{$engine->slug()}.{$model->slug()}.isUnlimited.boolean" => str_replace(':aiModel', $model->value, 'The :aiModel boolean value must be true or false.'),
                ];
            });
        })->toArray();
    }

    public function updateEntities($key, $value): void
    {
        $this->validate();
        $this->dispatch('updateEntities', $key, $value);

        $keys = explode('.', $key);

        $engine = data_get($keys, 1);

        $entity = data_get($keys, 2);

        $unitPrice = $this->entityUnitPrices[$engine][$entity];

        if (is_numeric($value)) {
            $this->costs[$engine][$entity]['credit'] = $value * $unitPrice;
        }

        if (Str::contains($key, 'isUnlimited')) {
            if ($value) {
                $this->costs[$engine][$entity]['credit'] = 0;
                $this->costs[$engine][$entity]['isUnlimited'] = true;
            } else {
                $this->costs[$engine][$entity]['isUnlimited'] = false;
                $this->costs[$engine][$entity]['credit'] = $this->entities[$engine][$entity]['credit'] * $unitPrice;

            }
        }

        $this->calculateTotals();
    }

    public function render(): View
    {
        return view('livewire.assign-view-credits');
    }
}
