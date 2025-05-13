<div class="flex">

    <div class="mx-auto w-full py-10 lg:w-2/3">
        <x-alert
            class="mb-8 p-4 text-xs shadow-none"
            variant="warn-fill"
        >
            @lang("We've revamped the plan management system to give you full control over your pricing strategies. Your users' credits will be migrated to the new pricing system, so you may need to review and update your pricing plans. New pricing plans won't be affected.")
            <a
                class="text-heading-foreground underline"
                href=" https://magicaidocs.liquid-themes.com/membership-plans-setup/"
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

        @if ($this->currentStepIs(1))
            <div class="w-full pt-5">
                <x-form-step
                    class="mb-4"
                    step="1"
                    label="{{ __('Global Settings') }}"
                />
                <div class="flex w-full gap-6">
                    <div class="mt-3 w-1/2">
                        <x-form.group
                            label="{{ __('Plan Name') }}"
                            tooltip="{{ __('Plan name') }}"
                            error="plan.name"
                        >
                            <x-form.text
                                class="{{ $errors->has('plan.name') ? 'border-2 border-rose-500' : '' }}"
                                id="name"
                                wire:model="plan.name"
                                placeholder="{{ __('Plan name') }}"
                            />
                        </x-form.group>
                    </div>
                    <div class="mt-3 w-1/2">
                        <x-form.group
                            label="{{ __('Plan Description') }}"
                            tooltip="{{ __('Plan Description') }}"
                            error="plan.description"
                        >
                            <x-form.textarea
                                class="{{ $errors->has('plan.description') ? 'border-2 border-rose-500' : '' }}"
                                wire:model="plan.description"
                                placeholder="{{ __('Plan Description') }}"
                            />
                        </x-form.group>
                    </div>
                </div>
                <div class="flex w-full gap-6">
                    <div class="mt-3 w-1/2">
                        <x-form.group
                            label="{{ __('Plan Features') }}"
                            tooltip="{{ __('Plan Features') }}"
                            error="plan.features"
                        >
                            <x-form.textarea
                                class="{{ $errors->has('plan.features') ? 'border-2 border-rose-500' : '' }}"
                                class:container="w-full mt-4"
                                wire:model="plan.features"
                                cols="30"
                                rows="10"
                                size="lg"
                                placeholder="{{ __('Separate with comma') }}"
                            />
                        </x-form.group>
                    </div>
                    <div class="mt-3 w-1/2">
                        <x-form.group
                            label="{{ __('Plan Status') }}"
                            tooltip="{{ __('Plan status') }}"
                            error="plan.active"
                        >
                            <x-form.checkbox
                                class:container="w-full mt-4"
                                wire:model="plan.active"
                                switcher
                            />
                        </x-form.group>
                    </div>
                </div>

                <x-form-step
                    class="mb-4 mt-4"
                    step="2"
                    label="{{ __('Pricing') }}"
                />

                <div class="flex w-full gap-6">

                    <div class="mt-5 w-1/2">
                        <x-form.group
                            label="{{ __('Price') }}"
                            tooltip="{{ __('Price') }}"
                            error="plan.price"
                        >
                            <x-form.stepper
                                class="{{ $errors->has('plan.price') ? 'border-2 border-rose-500' : '' }}"
                                wire:model="plan.price"
                                step="1"
                                placeholder="{{ __('Price') }}"
                            />
                        </x-form.group>
                    </div>
                </div>
            </div>
        @endif

        @if ($this->currentStepIs(2))
            @include('panel.admin.finance.plan.includes.step-fourth')
        @endif

        @include('panel.admin.finance.plan.includes.step-actions')

        <x-product-ids-list :gatewayProducts="$this->plan?->gatewayProducts" />
    </div>

</div>
