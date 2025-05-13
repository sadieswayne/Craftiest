@extends('panel.layout.settings')
@section('title', __('Login Settings'))
@section('titlebar_actions', '')
@section('additional_css')

@endsection

@section('settings')
	<form action="{{route("dashboard.admin.config.login.store")}}" method="POST">
		@csrf
		<x-form-step class="mb-3" step="1">
			{{ __('Recaptcha Settings') }}
		</x-form-step>
		<div class="row mb-4">
			<div class="col-md-12">
				<div class="mb-4">
					<x-alert class="rounde mb-4">
						<a href="https://scribehow.com/shared/Obtaining_reCAPTCHA_site_and_secret_keys_for_magicaicom__CMjndIDqTt26fz9xdhAQww" target="_blank">
							{{ __('Check the documentation.') }}
							<x-tabler-arrow-up-right class="size-4 inline align-text-bottom"/>
						</a>
					</x-alert>
					<x-forms.input
						class:container="mb-2"
						id="recaptcha_login"
						name="recaptcha_login"
						type="checkbox"
						:checked="$setting->recaptcha_login == 1"
						switcher
						label="{{ __('Login Recaptcha') }}"
					/>
					<x-forms.input
						class:container="mb-2"
						id="recaptcha_register"
						name="recaptcha_register"
						type="checkbox"
						:checked="$setting->recaptcha_register == 1"
						switcher
						label="{{ __('Register Recaptcha') }}"
					/>

					<x-alert class="mt-5 mb-3">
						<p>
							{{ __('Do not activate without ensuring that the key values are entered correctly.') }}
						</p>
					</x-alert>

					<div class="mt-1">
						<x-forms.input
							label="{{ __('Google Recaptcha Site Key') }}"
							class="form-control"
							id="recaptcha_sitekey"
							type="text"
							name="recaptcha_sitekey"
							value="{{ $setting->recaptcha_sitekey }}"
						/>
					</div>
					<div class="mt-1">
						<x-forms.input
							label="{{ __('Google Recaptcha Secret Key') }}"
							class="form-control"
							id="recaptcha_secretkey"
							type="text"
							name="recaptcha_secretkey"
							value="{{ $setting->recaptcha_secretkey }}"
						/>
					</div>
				</div>
			</div>
		</div>
		<x-form-step
			class="mb-3"
			step="2"
		>
			{{ __('Social Login Settings') }}
		</x-form-step>
		<div class="row mb-4">
			<div class="col-md-12">
				<div class="mb-4">
					<x-alert class="rounde mb-4">
						<a
							href="https://magicaidocs.liquid-themes.com/social-login"
							target="_blank"
						>
							{{ __('Check the documentation.') }}
							<x-tabler-arrow-up-right class="size-4 inline align-text-bottom"/>
						</a>
					</x-alert>
					<x-forms.input
						class:container="mb-2"
						id="facebook_active"
						name="facebook_active"
						type="checkbox"
						:checked="$setting->facebook_active == 1"
						switcher
						label="{{ __('Facebook') }}"
					/>
					<x-forms.input
						class:container="mb-2"
						id="google_active"
						name="google_active"
						type="checkbox"
						:checked="$setting->google_active == 1"
						switcher
						label="{{ __('Google') }}"
					/>
					<x-forms.input
						class:container="mb-2"
						id="github_active"
						name="github_active"
						type="checkbox"
						:checked="$setting->github_active == 1"
						switcher
						label="{{ __('Github') }}"
					/>
				</div>
			</div>
		</div>

		<x-form-step
			class="mb-3"
			step="3"
		>
			{{ __('Free Credits Usage Upon Registration') }}
		</x-form-step>
		<div class="row mb-4">
			<div class="col-md-12">
				<div class="mb-4">
					<div class="accordion " id="accordionExample">
						<div class="accordion-item">
							<h2 class="accordion-header" id="headingOne">
								<button
									class="accordion-button form-control"
									type="button"
									data-bs-toggle="collapse"
									data-bs-target="#collapseOne"
									aria-expanded="true"
									aria-controls="collapseOne"
								>
									{{ __('Credits') }}
								</button>
							</h2>
							<div
								id="collapseOne"
								class="accordion-collapse collapse"
								aria-labelledby="headingOne"
								data-bs-parent="#accordionExample"
							>
								<div class="accordion-body">
									@livewire('assign-view-credits', ['entities' => setting('freeCreditsUponRegistration', \App\Models\User::getFreshCredits())])
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<x-form-step
			class="mb-3"
			step="4"
		>
			{{ __('Limitations') }}
		</x-form-step>
		<div class="row mb-4">
			<div class="col-md-12">
				<div class="mb-4">
					<x-forms.input
						class:container="mb-2"
						id="limit"
						type="checkbox"
						name="daily_limit_enabled"
						:checked="$settings_two?->daily_limit_enabled == 1"
						label="{{ __('Apply daily limit on image generation') }}"
						switcher
					/>
					<div
						class="mb-4"
						id="countField"
						style="{{ $settings_two?->daily_limit_enabled == 1 ? '' : 'display:none' }}"
					>
						<label class="form-label">{{ __('Daily Image Limit Count') }}</label>
						<input
							class="form-control"
							id="allowed_images_count"
							type="text"
							name="allowed_images_count"
							value="{{ $settings_two?->allowed_images_count }}"
						>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="mb-4">
					<x-forms.input
						class:container="mb-2"
						id="daily_voice_limit_enabled"
						type="checkbox"
						name="daily_voice_limit_enabled"
						:checked="$settings_two?->daily_voice_limit_enabled == 1"
						label="{{ __('Apply daily limit on voice generation') }}"
						switcher
					/>
				</div>
			</div>
			<div class="col-md-12">
				<div
					class="mb-4"
					id="voiceCountField"
					style="{{ $settings_two?->daily_voice_limit_enabled == 1 ? '' : 'display:none' }}"
				>
					<label class="form-label">{{ __('Daily Voice Limit Count') }}</label>
					<input
						class="form-control"
						id="allowed_voice_count"
						type="text"
						name="allowed_voice_count"
						value="{{ $settings_two?->allowed_voice_count }}"
					>
				</div>
				<div class="mb-4">
					<x-forms.input
						id="login_without_confirmation"
						name="login_without_confirmation"
						type="checkbox"
						switcher
						type="checkbox"
						:checked="$setting->login_without_confirmation == 1"
						label="{{ __('Disable Login Without Confirmation') }}"
						tooltip="{{ __('If this is enabled users cannot login unless they confirm their emails.') }}"
					/>
				</div>
			</div>
		</div>

		<x-form-step
			class="mb-3"
			step="5"
		>
			{{ __('Other Settings') }}
		</x-form-step>
		<div class="row mb-4">
			<div class="col-md-12">
				<div class="mb-4">
					<label class="form-label">{{ __('Login With OTP') }}
						<x-info-tooltip text="{{ __('Make sure your SMTP settings are configured before activating this.') }}"/>
					</label>
					<select
						class="form-select"
						id="login_with_otp"
						name="login_with_otp"
					>
						<option
							value="1"
							{{ $setting->login_with_otp ? 'selected' : '' }}
						>
							{{ __('Active') }}</option>
						<option
							value="0"
							{{ !$setting->login_with_otp ? 'selected' : '' }}
						>
							{{ __('Passive') }}</option>
					</select>
				</div>
			</div>
			<div class="col-md-12">
				<div class="mb-4">
					<label class="form-label">{{ __('Registration Active') }}</label>
					<select
						class="form-select"
						id="register_active"
						name="register_active"
					>
						<option
							value="1"
							{{ $setting->register_active == 1 ? 'selected' : '' }}
						>
							{{ __('Active') }}</option>
						<option
							value="0"
							{{ $setting->register_active == 0 ? 'selected' : '' }}
						>
							{{ __('Passive') }}</option>
					</select>
				</div>
			</div>
		</div>
		<div class="col-12 mt-4">
			<button
				class="btn btn-primary w-full"
				type="submit"
			>
				{{ __('Save') }}
			</button>
		</div>
	</form>
@endsection

@push('script')

@endpush
