@php
	use App\Enums\Plan\PlanType;
	use App\Enums\Plan\FrequencyEnum;
@endphp

<div class="w-full space-y-7">

	<div class="row gap-y-7">
		<div class="col-12">
			<x-form-step
				class="mb-0"
				step="1"
				label="{{ __('Global Settings') }}"
			/>
		</div>
		<div class="col-12 col-sm-6">
			<x-form.group
				label="{{ __('Plan Name') }}"
				tooltip="{{ __('Plan name') }}"
				error="plan.name"
			>
				<x-form.text
					wire:model="plan.name"
					placeholder="{{ __('Plan name') }}"
					required
					maxlength="190"
					size="lg"
				/>
			</x-form.group>
		</div>

		<div class="col-12 col-sm-6">
			<x-form.group
				label="{{ __('Plan Description') }}"
				tooltip="{{ __('Plan description') }}"
				error="plan.description"
			>
				<x-form.text
					class:container="w-full mt-4"
					wire:model="plan.description"
					placeholder="{{ __('Plan description') }}"
					size="lg"
					maxlength="15000"
					required
				/>
			</x-form.group>
		</div>

		<div class="col-12 col-sm-6">
			<x-form.group
				label="{{ __('Plan Features') }}"
				error="plan.features"
			>
				<x-form.textarea
					class:container="w-full mt-4"
					wire:model="plan.features"
					cols="30"
					rows="10"
					size="lg"
					label="{{ __('Plan Features') }}"
					placeholder="{{ __('Separate with comma') }}"
					required
					maxlength="15000"
				/>
			</x-form.group>
		</div>

		<div class="col-12 col-sm-6 space-y-3">
			<x-form.group
				label="{{ __('Default ai model') }}"
				error="plan.default_ai_model"
			>
				<x-form.select
					class:container="w-full mt-4"
					wire:model="plan.default_ai_model"
					required
				>
					<option value="">{{ __('Select Default AI Model') }}</option>
					@foreach ($models as $aiModel)
						<option value="{{ $aiModel->key->value }}">
							{{ $aiModel->key->value }}
						</option>
					@endforeach
				</x-form.select>
			</x-form.group>

			<div>
				<x-form.group
					label="{{ __('Template Access') }}"
					tooltip="{{ __('Template Access') }}"
					error="plan.plan_type"
				>
					<x-form.select
						wire:model="plan.plan_type"
						required
					>
						<option value="">{{ __('Select Plan Type') }}</option>
						@foreach (PlanType::cases() as $key)
							<option value="{{ $key->value }}">{{ __($key->label()) }}</option>
						@endforeach
					</x-form.select>
				</x-form.group>
			</div>

			<x-form.group
				no-group-label
				error="plan.is_featured"
			>
				<x-form.checkbox
					class:container="w-full mt-4"
					wire:model="plan.is_featured"
					label="{{ __('Featured Plan') }}"
					tooltip="{{ __('Featured Plan') }}"
					switcher
				/>
			</x-form.group>

			<x-form.group
				no-group-label
				error="plan.active"
			>
				<x-form.checkbox
					class:container="w-full mt-4"
					wire:model="plan.active"
					label="{{ __('Active') }}"
					tooltip="{{ __('Plan status') }}"
					switcher
				/>
			</x-form.group>
		</div>
	</div>

	<div class="row gap-y-7">
		<div class="col-12">
			<x-form-step
				class="mb-0"
				step="2"
				label="{{ __('Pricing') }}"
			/>
		</div>
		<div class="col-12 col-sm-6">
			<x-form.group
				label="{{ __('Price') }}"
				tooltip="{{ __('Price') }}"
				error="plan.price"
			>
				<x-form.stepper
					wire:model="plan.price"
					type="number"
					step="1"
					placeholder="{{ __('Price') }}"
				/>
				<x-alert
					class="mt-1"
					variant="danger"
				>
					<p>
						@lang('Price is a sensitive field. Changing the price will cancel the existing subscriptions. Please be careful.')
					</p>
				</x-alert>
			</x-form.group>
		</div>
		<div class="col-12 col-sm-6">
			<x-form.group
				label="{{ __('Renewal Type') }}"
				tooltip="{{ __('Renewal type of a plan, it could be monthly, yearly etc') }}"
				error="plan.frequency"
			>
				<x-form.select
					class:container="w-full "
					class="border-2 border-red-400"
					wire:model="plan.frequency"
					required
					size="lg"
				>
					<option value="">{{ __('Select Frequency') }}</option>

					@foreach (FrequencyEnum::cases() as $key)
						<option value="{{ $key->value }}">{{ __($key->label()) }}</option>
					@endforeach
				</x-form.select>
				<x-alert
					class="mt-1"
					variant="danger"
				>
					<p>
						@lang('Renewal Type is a sensitive field. Changing the Renewal Type will cancel the existing subscriptions. Please be careful.')
					</p>
				</x-alert>
			</x-form.group>
		</div>

		<div
			class="col-12 col-sm-6 space-y-5"
			x-data="{ isTeamPlan: {{ $plan?->is_team_plan ? 'true' : 'false' }} }"
		>
			<x-form.group
				no-group-label
				error="plan.is_team_plan"
			>
				<x-form.checkbox
					class:container="mb-4"
					wire:model="plan.is_team_plan"
					label="{{ __('Enable Team Plan') }}"
					tooltip="{{ __('Enable Team Plan') }}"
					size="lg"
					x-model="isTeamPlan"
					switcher
				/>
			</x-form.group>

			<div
				x-show="isTeamPlan"
				x-cloak
			>
				<x-form.group
					label="{{ __('Number of Seats') }}"
					tooltip="{{ __('Number of Seats') }}"
					error="plan.plan_allow_seat"
				>
					<x-form.stepper
						wire:model="plan.plan_allow_seat"
						step="1"
						required
						min="0"
					/>
				</x-form.group>

			</div>
		</div>
		<div
			class="col-12 col-sm-6 space-y-5"
			x-data="{ isTrial: {{ (int) $plan?->trial_days > 0 ? 'true' : 'false' }} }"
		>
			<x-form.group no-group-label>
				<x-form.checkbox
					class:container="mb-4"
					label="{{ __('Trial') }}"
					switcher
					x-model="isTrial"
					checked="{{ (int) $plan?->trial_days > 0 }}"
				/>
			</x-form.group>
			<div
				id="countField"
				x-show="isTrial"
				x-cloak
			>
				<x-form.group
					label="{{ __('Trial days') }}"
					tooltip="{{ __('Trial days') }}"
					error="plan.trial_days"
				>
					<x-form.stepper
						wire:model="plan.trial_days"
						step="1"
						size="lg"
						min="0"
					/>
				</x-form.group>

            </div>
        </div>

		<div class="col-12 col-sm-6 space-y-5">
			<x-form.group no-group-label>
				<x-form.checkbox
					class:container="mb-4"
					wire:model="plan.reset_credits_on_renewal"
					label="{{ __('Reset Credits on Renewal') }}"
					tooltip="{{ __('When enabled, the user credits will be reset to the plan credits on renewal. If disabled, the user credits will be carried over to the next renewal.') }}"
					switcher
					checked="{{ $plan?->reset_credits_on_renewal }}"
				/>
			</x-form.group>
		</div>
    </div>

	<div class="row gap-y-7">
		<div class="col-12">
			<x-form-step
				class="mb-0"
				step="3"
				label="{{ __('Users API Key Option') }}"
			/>
			<div class="form-label mt-5">
				{{ __('Enabling this feature in a plan will require users to provide their own API keys instead of using the admin API key for continued functionality.') }}
			</div>
			<div
				class="col-12 col-sm-6 space-y-5"
			>
				<x-form.group
					class:container="mb-4"
					no-group-label
					error="plan.user_api"
				>
					<x-form.checkbox
						class:container="w-full mt-4"
						wire:model="plan.user_api"
						label="{{ __('User API Key') }}"
						tooltip="{{ __('User API Key') }}"
						switcher
					/>
				</x-form.group>
			</div>
		</div>
	</div>

	<!-- Hidden Plan-->

	<div class="row gap-y-7">
		<div class="col-12">
			<x-form-step
				class="mb-0"
				step="3"
				label="{{ __('Private Configuration') }}"
			/>
		</div>
		<div
			class="col-12 col-sm-12 space-y-5"
			x-data="{ isHiddenPlan: {{ $plan?->hidden ? 'true' : 'false' }} }"
		>
			<x-form.group
				no-group-label
				error="plan.hidden"
			>
				<x-form.checkbox
					class:container="mb-4"
					wire:model="plan.hidden"
					label="{{ __('Private Plan') }}"
					tooltip="{{ __('Private Plan') }}"
					size="lg"
					x-model="isHiddenPlan"
					switcher
				/>
			</x-form.group>

			<div
				x-show="isHiddenPlan"
				x-cloak
			>
				<div class="row">
					<div class="col-12 col-sm-6">
						<x-form.group
							label="{{ __('Max Subscriber') }}"
							tooltip="{{ __('Maximum number of subscribers for the plan. If you want it to be unlimited, select -1.') }}"
							error="plan.max_subscribe"
						>
							<x-form.stepper
								wire:model="plan.max_subscribe"
								type="number"
								step="1"
								min="-1"
								placeholder="{{ __('Max Subscriber') }}"
							/>
						</x-form.group>
					</div>
					<div class="col-12 col-sm-6">
						<x-form.group
							label="{{ __('End Date') }}"
							tooltip="{{ __('Please enter an end date. If you want it to be unlimited, leave the date field empty.') }}"
							error="plan.last_date"
						>
							<x-form.text
								class:container="w-full mt-4"
								wire:model="plan.last_date"
								size="lg"
								type="date"
								required
							/>
						</x-form.group>
					</div>
				</div>

				<div x-data="{ value: @entangle('plan.hidden_url'), copied: false }" class="mt-4">
					<x-form.group
						class="relative"
						label="{{ __('Hidden Url') }}"
						tooltip="{{ __('A URL will be generated here after the plan is saved.') }}"
						error="plan.hidden_url"
					>
						<div class="flex items-center">
							<x-form.text
								class:container="w-full"
								wire:model="plan.hidden_url"
								size="lg"
								type="text"
								disabled
							/>

							<!-- Copy Icon -->
							<button
								@click="navigator.clipboard.writeText(value).then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
								class="ml-2 p-2 text-gray-500 hover:text-gray-700 focus:outline-none"
								title="Copy to clipboard"
							>
								<!-- Copy Icon (SVG) -->
								<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M8 8h8M4 4v16c0 1.104.896 2 2 2h12a2 2 0 002-2V8l-6-6H6a2 2 0 00-2 2z"/>
								</svg>
							</button>
						</div>

						<!-- Copied Notification -->
						<span x-show="copied" x-transition class="text-green-500 text-sm mt-1 block">
            {{ __('Copied!') }}
        </span>
					</x-form.group>
				</div>


			</div>
		</div>
	</div>
</div>
