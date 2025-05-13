<?php

namespace App\Livewire\Admin\Finance\Plan;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Models\Entity;
use App\Enums\Plan\PlanType;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Finance\PaymentProcessController;
use App\Livewire\Concerns\WithSteps;
use App\Models\Gateways;
use App\Models\Plan;
use App\Services\Common\MenuService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class SubscriptionPlanCreate extends Component
{
    use WithSteps;

    protected int $maxStep = 4;

    public ?Plan $plan = null;

    public array $entities = [];

    public ?Collection $gatewaysToCreatePriceIds = null;

    public function mount($plan = null): void
    {
        $this->step = 1;
        $this->useOrCreatePlan($plan);
        $this->privatePlanData();
    }

    #[On('updateEntities')]
    public function updateEntities($key, $value): void
    {
        $this->entities[$key] = $value;
    }

    public function rules(): array
    {
        return array_merge([
            // Step 1
            'plan.active'                        => 'required_if:step,1|boolean',
            'plan.name'                          => 'required_if:step,1|string|max:190',
            'plan.description'                   => 'required_if:step,1|string|max:15000',
            'plan.features'                      => 'required_if:step,1|string|max:15000',
            'plan.price'                         => 'required_if:step,1|numeric|min:0',
            'plan.frequency'                     => 'required_if:step,1|string|max:190',
            'plan.is_team_plan'                  => 'required_if:step,1|nullable|boolean',
            'plan.plan_allow_seat'               => 'nullable|numeric|min:0',
            'plan.trial_days'                    => 'required_if:step,1|numeric|min:0',
            'plan.is_featured'                   => 'required_if:step,1|nullable|boolean',
            'plan.user_api'                      => 'required_if:step,1|nullable|boolean',
            'plan.plan_type'                     => 'required_if:step,1|in:' . implode(',', PlanType::getValues()),
            'plan.default_ai_model'              => 'required_if:step,1|string|max:255',
            'plan.hidden'                        => 'boolean',
            'plan.max_subscribe'                 => 'integer|min:-1|nullable',
            'plan.reset_credits_on_renewal'      => 'boolean',
            'plan.last_date'                     => 'date|nullable',
            'plan.hidden_url'                    => 'nullable',
        ],
            // Step 2
            $this->rulesOfPlanAiTools(),
            $this->rulesOfPlanFeature(),
            // Step 3
            $this->plan->openAiItemsRules('plan.open_ai_items.', 'nullable|boolean'),
            // Step 4 rules and validation in child livewire component
        );
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

    private function useOrCreatePlan(?Plan $plan): void
    {
        $this->plan = ($plan && $plan->exists) ? $plan : Plan::createFreshPlan();
    }

    private function getModels(): Collection
    {
        return Entity::query()
            ->isEnabled()
            ->whereNotIn('key', [
                EntityEnum::WHISPER_1,
                EntityEnum::DALL_E_2,
                EntityEnum::DALL_E_3,
                EntityEnum::TTS_1,
                EntityEnum::TTS_1_HD,
            ])->whereIn('engine', [
                EngineEnum::OPEN_AI,
                EngineEnum::GEMINI,
                EngineEnum::ANTHROPIC,
            ])->get();
    }

    public function stepTitle(int $num): string
    {
        return match ($num) {
            1 => 'Plan Details',
            2 => 'Features',
            3 => 'Templates',
            4 => 'Limits',
        };
    }

    public function getPercent(): int
    {
        return match ($this->currentStep()) {
            1       => 25,
            2       => 50,
            3       => 75,
            default => 100,
        };
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
            PaymentProcessController::saveGatewayProducts($this->plan, $this->gatewaysToCreatePriceIds);
        }
        app(MenuService::class)->regenerate();

        return redirect(route('dashboard.admin.finance.plan.index'))->with([
            'message' => 'Plan ' . $this->plan->name . ' successfully.',
            'type'    => 'success',
        ]);
    }

    public function rulesOfPlanAiTools(): array
    {
        $data = $this->planAiToolsMenu();

        return collect($data)->mapWithKeys(function ($item) {
            return ['plan.plan_ai_tools.' . $item['key'] => 'nullable|sometimes|boolean'];
        })->toArray();
    }

    public function rulesOfPlanFeature(): array
    {
        $data = $this->planFeatureMenu();

        return collect($data)->mapWithKeys(function ($item) {
            return ['plan.plan_features.' . $item['key'] => 'nullable|sometimes|boolean'];
        })->toArray();
    }

    public function planAiToolsMenu(): array
    {
        return once(static fn () => MenuService::planAiToolsMenu());
    }

    public function planFeatureMenu(): array
    {
        return once(static fn () => MenuService::planFeatureMenu());
    }

    public function render(): View
    {
        return view('livewire.admin.finance.plan.subscription-plan-create', [
            'models'          => $this->getModels(),
            'aiEngines'       => EngineEnum::whereHasEnabledModels(),
            'planAiToolsMenu' => $this->planAiToolsMenu(),
            'planFeatureMenu' => $this->planFeatureMenu(),
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
        // check if price_id did not create for any gateway
        $missingPriceGatwayCods = collect($this->plan->gatewayProducts)
            ->filter(fn ($gateway) => ! $gateway->price_id)
            ->pluck('gateway_code')
            ->toArray();
        if (count($missingPriceGatwayCods) > 0) {
            $this->gatewaysToCreatePriceIds = Gateways::whereIn('code', $missingPriceGatwayCods)->get();
        }

        return ! $this->plan?->exists || $this->plan->isDirty(['price', 'frequency']) || count($missingPriceGatwayCods) > 0;
    }

    private function privatePlanData(): void
    {
        if (is_null($this->plan->hidden_url)) {
            $this->plan->hidden_url = Helper::parseUrl(
                config('app.url'),
                'plan/private',
                $this->plan->type,
                Str::random(20)
            );
        }
    }
}
