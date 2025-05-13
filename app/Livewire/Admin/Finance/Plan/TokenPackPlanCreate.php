<?php

namespace App\Livewire\Admin\Finance\Plan;

use App\Domains\Engine\Enums\EngineEnum;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Finance\PaymentProcessController;
use App\Livewire\Concerns\WithSteps;
use App\Models\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Attributes\On;
use Livewire\Component;

class TokenPackPlanCreate extends Component
{
    use WithSteps;

    public ?Plan $plan;

    public array $entities = [];

    protected int $maxStep = 2;

    public static function rules(): array
    {
        return [
            // Step 1
            'plan.active'             => 'boolean',
            'plan.name'               => 'required_if:step,1|string|max:190',
            'plan.description'        => 'required_if:step,1|nullable|string|max:15000',
            'plan.price'              => 'required_if:step,1|numeric|min:0',
            'plan.features'           => 'required_if:step,1|string|max:15000',
            'plan.type'               => 'nullable',
            'plan.plan_ai_tools'      => 'nullable',
            'plan.plan_features'      => 'nullable',
            // Step 2 rules and validation in child livewire component
        ];
    }

    public function mount($plan = null): void
    {
        $this->useOrCreateTokenPackPlan($plan);
    }

    #[On('updateEntities')]
    public function updateEntities($key, $value): void
    {
        $this->entities[$key] = $value;
    }

    public function nextStep(): void
    {
        $this->validate();

        if (! $this->hasNextStep()) {
            $this->submit();

            return;
        }

        $this->toNextStep();

    }

    public function submit(): RedirectResponse|Redirector
    {
        if (Helper::appIsDemo()) {
            return redirect(route('dashboard.admin.finance.plan.index'))->with([
                'message' => 'This feature is disabled in demo mode.',
                'type'    => 'error',
            ]);
        }
        $isSensitiveDataChanged = $this->isSensitiveDataChanged();
        $this->changePlanValuesWithSuppliedEntities();
        $this->plan->save();
        if ($isSensitiveDataChanged) {
            PaymentProcessController::saveGatewayProducts($this->plan);
        }

        return redirect(route('dashboard.admin.finance.plan.index'))->with([
            'message' => 'Plan successfully updated.',
            'type'    => 'success',
        ]);
    }

    #[NoReturn]
    private function useOrCreateTokenPackPlan(?Plan $plan): void
    {
        $this->plan = ($plan && $plan->exists) ? $plan : Plan::createFreshTokenPackPlan();
    }

    public function stepTitle(int $num): string
    {
        return match ($num) {
            1 => 'Plan Details',
            2 => 'Limits',
        };
    }

    public function getPercent(): int
    {
        return match ($this->currentStep()) {
            1       => 50,
            default => 100,
        };
    }

    public function render(): View
    {
        return view('livewire.admin.finance.plan.token-pack-plan-create', [
            'aiEngines' => EngineEnum::whereHasEnabledModels(),
        ]);
    }

    private function changePlanValuesWithSuppliedEntities(): void
    {
        foreach ($this->entities as $key => $value) {
            $keys = explode('.', $key);
            $tmp = $this->plan->ai_models;
            $tmp[$keys[1]][$keys[2]][$keys[3]] = $value;
            $this->plan->ai_models = $tmp;
        }
    }

    private function isSensitiveDataChanged(): bool
    {
        // $this->plan->isDirty([]);
        // always update gateway products for token pack
        return true;
    }
}
