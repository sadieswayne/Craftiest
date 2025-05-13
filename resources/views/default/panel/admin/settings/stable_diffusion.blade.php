@extends('panel.layout.settings')
@section('title', __('Stable Diffusion Settings'))
@section('titlebar_subtitle', __('This API key is used for these features: AI Image, AI Article Wizard, Advanced Image Editor'))

@section('settings')
    <form
            id="settings_form"
            onsubmit="return openaiSettingsSave();"
            enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('Stable Diffusion Settings') }}</h3>
        <div class="row">
            <!-- TODO OPENAI API KEY -->
            @if ($app_is_demo)
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('OpenAI API Secret') }}</label>
                        <input
                                class="form-control"
                                id="openai_api_secret"
                                type="text"
                                name="openai_api_secret"
                                value="*********************"
                        >
                    </div>
                </div>
            @else
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('OpenAI API Secret') }}</label>
                        <input
                                class="form-control"
                                id="openai_api_secret"
                                type="text"
                                name="openai_api_secret"
                                value="{{ $setting->openai_api_secret }}"
                        >
                    </div>
                </div>
            @endif

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Default Openai Model') }}</label>
                    <select
                            class="form-select"
                            id="openai_default_model"
                            name="openai_default_model"
                    >
                        <!--
                   <option value="text-ada-001" {{ $setting->openai_default_model == 'text-ada-001' ? 'selected' : null }}>{{ __('Ada (Cheapest &amp; Fastest)') }}</option>
                   <option value="text-babbage-001" {{ $setting->openai_default_model == 'text-babbage-001' ? 'selected' : null }}>{{ __('Babbage') }}</option>
                   <option value="text-curie-001" {{ $setting->openai_default_model == 'text-curie-001' ? 'selected' : null }}>{{ __('Curie') }}</option>
                   -->
                        <option
                                value="{{\App\Domains\Entity\Enums\EntityEnum::TEXT_DAVINCI_003->value}}"
                                {{ $setting->openai_default_model == \App\Domains\Entity\Enums\EntityEnum::TEXT_DAVINCI_003->value ? 'selected' : null }}
                        >
                            {{ __('Davinci (Most Expensive &amp; Most Capable)') }}</option>
                        <option
                                value="{{\App\Domains\Entity\Enums\EntityEnum::GPT_3_5_TURBO->value}}"
                                {{ $setting->openai_default_model == \App\Domains\Entity\Enums\EntityEnum::GPT_3_5_TURBO->value ? 'selected' : null }}
                        >
                            {{ __('ChatGPT (Most Expensive & Fastest & Most Capable)') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Default Openai Language') }}</label>
                    <select
                            class="form-select"
                            id="openai_default_language"
                            name="openai_default_language"
                    >
                        @include('panel.admin.settings.languages')
                    </select>
                </div>
            </div>

            {{-- <div class="col-md-6">
				<div class="mb-3">
					<label class="form-label">{{ __('Maximum Input Length') }}</label>
					<input type="number" class="form-control" id="openai_max_input_length"
						name="openai_max_input_length" min="10" max="1500"
						value="{{ $setting->openai_max_input_length }}" required>
					<span
						class="block p-2 mt-1 rounded-md text-danger bg-[rgba(var(--tblr-danger-rgb),0.1)]">{{ __('In Characters') }}</span>
				</div>
			</div> --}}

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Image Count') }}</label>
                    <input
                            class="form-control"
                            id="openai_max_output_length"
                            type="number"
                            name="openai_max_output_length"
                            min="1"
                            max="10"
                            value="{{ $setting->openai_max_output_length }}"
                            required
                    >
                    <span
                            class="text-danger mt-1 block rounded-md bg-[rgba(var(--tblr-danger-rgb),0.1)] p-2">{{ __('In Words. OpenAI has a hard limit based on Token limits for each model. Refer to OpenAI documentation to learn more. As a recommended by OpenAI, max result length is capped at 1500 words') }}</span>
                </div>
            </div>

        </div>
        <button
                class="btn btn-primary w-full"
                id="settings_button"
                form="settings_form"
        >
            {{ __('Save') }}
        </button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
@endpush
