<div class="flex">

    <livewire:offline />

    <div class="mx-auto w-full py-10 lg:w-2/3">
        <x-alert
            class="mb-8 p-4 text-xs shadow-none"
            variant="warn-fill"
        >
            @lang("We've revamped the plan management system to give you full control over your pricing strategies. Your users' credits will be migrated to the new pricing system, so you may need to review and update your pricing plans. New pricing plans won't be affected.")
            <a
                class="text-heading-foreground underline"
                href="https://magicaidocs.liquid-themes.com/membership-plans-setup/"
            >
                @lang('Learn more >')
            </a>
        </x-alert>

        <div class="lqd-steps mb-11 flex flex-col gap-7">
            <div class="lqd-steps-steps flex items-center justify-between gap-3">
                @for ($i = 1; $i <= $this->totalStep(); $i++)
                    <div @class([
                        'lqd-step group/step flex flex-col items-center gap-3 rounded p-2 text-center text-3xs font-semibold capitalize text-heading-foreground transition-colors disabled:pointer-events-none disabled:opacity-50 max-md:w-1/2 sm:flex-row sm:items-start sm:text-start lg:min-w-32',
                        'active' => $this->currentStep() >= $i,
                    ])>
                        <span
                            class="size-[21px] inline-grid place-items-center rounded-md bg-primary/10 text-primary transition-colors group-[&.active]/step:bg-primary group-[&.active]/step:text-primary-foreground dark:bg-heading-foreground/5 dark:text-heading-foreground"
                        >
                            {{ $i }}
                        </span>
                        @lang($this->stepTitle($i))
                    </div>
                @endfor
            </div>
            <div class="lqd-step-progress relative h-1.5 w-full overflow-hidden rounded-lg bg-heading-foreground/5">
                <div
                    class="lqd-step-progress-bar absolute start-0 top-0 h-full w-0 rounded-full bg-gradient-to-r from-gradient-from to-gradient-to transition-all"
                    style="width: {{ $this->getPercent() }}%"
                ></div>
            </div>
        </div>

        @includeWhen($this->currentStepIs(1), 'panel.admin.finance.plan.includes.step-first')
        @includeWhen($this->currentStepIs(2), 'panel.admin.finance.plan.includes.step-second')
        @includeWhen($this->currentStepIs(3), 'panel.admin.finance.plan.includes.step-third')
        @includeWhen($this->currentStepIs(4), 'panel.admin.finance.plan.includes.step-fourth')

        @include('panel.admin.finance.plan.includes.step-actions')

        <x-product-ids-list :gatewayProducts="$this->plan?->gatewayProducts" />
    </div>
</div>
