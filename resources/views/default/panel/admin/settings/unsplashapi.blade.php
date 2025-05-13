@extends('panel.layout.settings')
@section('title', __('Unsplash API Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('This API key is used for these features: AI Article Wizard'))

@section('additional_css')
    <link
        href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
        rel="stylesheet"
    />
@endsection

@section('settings')
    <form
        id="settings_form"
        onsubmit="return unsplashSettingsSave();"
        enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __(\App\Domains\Engine\Enums\EngineEnum::UNSPLASH->label().' API Settings') }}</h3>
        <div class="row">
            <!-- TODO Unsplash api key -->
            @if ($app_is_demo)
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __(':label API Key', ['label' => \App\Domains\Engine\Enums\EngineEnum::UNSPLASH->label()]) }}</label>
                        <input
                            class="form-control"
                            id="unsplash_api_key"
                            type="text"
                            name="unsplash_api_key"
                            value="*********************"
                        >
                    </div>
                </div>
            @else
                <div class="col-md-12">
                    <div
                        class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                        <label class="form-label">{{ __(':label API Key', ['label' => \App\Domains\Engine\Enums\EngineEnum::UNSPLASH->label()]) }}
                            <x-alert class="mt-2">
                                <x-button
                                    variant="link"
                                    href="https://unsplash.com/documentation#creating-a-developer-account"
                                    target="_blank"
                                >
                                    {{ __('Get an API key') }}
                                </x-button>
                            </x-alert>
                        </label>
                        <input
                            class="form-control"
                            id="unsplash_api_key"
                            type="text"
                            name="unsplash_api_key"
                            value="{{ $settings_two->unsplash_api_key }}"
                            required
                        >
                        <x-alert
                            class="mt-2"
                            variant="lg"
                        >
                            <p>
                                {{ __('Please ensure that your Unsplash api key is fully functional and billing defined on your Unsplash account.') }}
                            </p>
                        </x-alert>
                        <a
                            class="btn btn-primary mb-2 mt-2 w-full"
                            href="{{ route('dashboard.admin.settings.unsplashapi.test') }}"
                            target="_blank"
                        >
                            {{ __('After Saving Setting, Click Here to Test Your api key') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- <div class="col-md-12">
				<div class="mb-3">
					<label class="form-label">{{__('Default stablediffusion Language')}}</label>
					<select class="form-select" name="stablediffusion_default_language" id="stablediffusion_default_language">
						<option value="ar-AE" {{$settings_two->stablediffusion_default_language == 'ar-AE' ? 'selected' : null}}>{{__('Arabic')}}</option>
						<option value="cmn-CN"{{$settings_two->stablediffusion_default_language == 'cmn-CN' ? 'selected' : null}}>{{__('Chinese (Mandarin)')}}</option>
						<option value="hr-HR" {{$settings_two->stablediffusion_default_language == 'hr-HR' ? 'selected' : null}}>{{__('Croatian (Croatia)')}}</option>
						<option value="cs-CZ" {{$settings_two->stablediffusion_default_language == 'cs-CZ' ? 'selected' : null}}>{{__('Czech (Czech Republic)')}}</option>
						<option value="da-DK" {{$settings_two->stablediffusion_default_language == 'da-DK' ? 'selected' : null}}>{{__('Danish (Denmark)')}}</option>
						<option value="nl-NL" {{$settings_two->stablediffusion_default_language == 'nl-NL' ? 'selected' : null}}>{{__('Dutch (Netherlands)')}}</option>
						<option value="en-US" {{$settings_two->stablediffusion_default_language == 'en-US' ? 'selected' : null}}>{{__('English (USA)')}}</option>
						<option value="et-EE" {{$settings_two->stablediffusion_default_language == 'et-EE' ? 'selected' : null}}>{{__('Estonian (Estonia)')}}</option>
						<option value="fi-FI" {{$settings_two->stablediffusion_default_language == 'fi-FI' ? 'selected' : null}}>{{__('Finnish (Finland)')}}</option>
						<option value="fr-FR" {{$settings_two->stablediffusion_default_language == 'fr-FR' ? 'selected' : null}}>{{__('French (France)')}}</option>
						<option value="de-DE" {{$settings_two->stablediffusion_default_language == 'de-DE' ? 'selected' : null}}>{{__('German (Germany)')}}</option>
						<option value="el-GR" {{$settings_two->stablediffusion_default_language == 'el-GR' ? 'selected' : null}}>{{__('Greek (Greece)')}}</option>
						<option value="he-IL" {{$settings_two->stablediffusion_default_language == 'he-IL' ? 'selected' : null}}>{{__('Hebrew (Israel)')}}</option>
						<option value="hi-IN" {{$settings_two->stablediffusion_default_language == 'hi-IN' ? 'selected' : null}}>{{__('Hindi (India)')}}</option>
						<option value="hu-HU" {{$settings_two->stablediffusion_default_language == 'hu-HU' ? 'selected' : null}}>{{__('Hungarian (Hungary)')}}</option>
						<option value="is-IS" {{$settings_two->stablediffusion_default_language == 'is-IS' ? 'selected' : null}}>{{__('Icelandic (Iceland)')}}</option>
						<option value="id-ID" {{$settings_two->stablediffusion_default_language == 'id-ID' ? 'selected' : null}}>{{__('Indonesian (Indonesia)')}}</option>
						<option value="it-IT" {{$settings_two->stablediffusion_default_language == 'it-IT' ? 'selected' : null}}>{{__('Italian (Italy)')}}</option>
						<option value="ja-JP" {{$settings_two->stablediffusion_default_language == 'ja-JP' ? 'selected' : null}}>{{__('Japanese (Japan)')}}</option>
						<option value="kk-KZ" {{$settings_two->stablediffusion_default_language == 'kk-KZ' ? 'selected' : null}}>{{__('Kazakh')}}</option>
						<option value="ko-KR" {{$settings_two->stablediffusion_default_language == 'ko-KR' ? 'selected' : null}}>{{__('Korean (South Korea)')}}</option>
						<option value="ms-MY" {{$settings_two->stablediffusion_default_language == 'ms-MY' ? 'selected' : null}}>{{__('Malay (Malaysia)')}}</option>
						<option value="nb-NO" {{$settings_two->stablediffusion_default_language == 'nb-NO' ? 'selected' : null}}>{{__('Norwegian (Norway)')}}</option>
						<option value="pl-PL" {{$settings_two->stablediffusion_default_language == 'pl-PL' ? 'selected' : null}}>{{__('Polish (Poland)')}}</option>
						<option value="pt-BR" {{$settings_two->stablediffusion_default_language == 'pt-BR' ? 'selected' : null}}>{{__('Portuguese (Brazil)')}}</option>
						<option value="pt-PT" {{$settings_two->stablediffusion_default_language == 'pt-PT' ? 'selected' : null}}>{{__('Portuguese (Portugal)')}}</option>
						<option value="ro-RO" {{$settings_two->stablediffusion_default_language == 'ro-RO' ? 'selected' : null}}>{{__('Romanian (Romania)')}}</option>
						<option value="ru-RU" {{$settings_two->stablediffusion_default_language == 'ru-RU' ? 'selected' : null}}>{{__('Russian (Russia)')}}</option>
						<option value="sl-SI" {{$settings_two->stablediffusion_default_language == 'sl-SI' ? 'selected' : null}}>{{__('Slovenian (Slovenia)')}}</option>
						<option value="es-ES" {{$settings_two->stablediffusion_default_language == 'es-ES' ? 'selected' : null}}>{{__('Spanish (Spain)')}}</option>
						<option value="sw-KE" {{$settings_two->stablediffusion_default_language == 'sw-KE' ? 'selected' : null}}>{{__('Swahili (Kenya)')}}</option>
						<option value="sv-SE" {{$settings_two->stablediffusion_default_language == 'sv-SE' ? 'selected' : null}}>{{__('Swedish (Sweden)')}}</option>
						<option value="tr-TR" {{$settings_two->stablediffusion_default_language == 'tr-TR' ? 'selected' : null}}>{{__('Turkish (Turkey)')}}</option>
						<option value="vi-VN" {{$settings_two->stablediffusion_default_language == 'vi-VN' ? 'selected' : null}}>{{__('Vietnamese (Vietnam)')}}</option>
					</select>
				</div>
			</div> --}}

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
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
