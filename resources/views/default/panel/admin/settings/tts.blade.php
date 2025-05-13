@extends('panel.layout.settings')
@section('title', __('TTS Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('This API key is used for these features: AI Voiceover, AI Speech to Text'))

@section('additional_css')
    <link
            href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
            rel="stylesheet"
    />
@endsection

@section('settings')
    <form
            id="settings_form"
            onsubmit="return ttsSettingsSave();"
            enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('TTS Settings') }}</h3>
        <div class="row">

            <div class="col-md-12">
				@includeFirst(['speechify-tts::setting', 'panel.admin.settings.particles.setting', 'vendor.empty'])
                @includeFirst(['azure-tts::azure-setting', 'panel.admin.settings.particles.azure-setting', 'vendor.empty'])

                <x-card
                        class="mb-3 w-full"
                        size="sm"
                >
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input
                                    class="form-check-input"
                                    id="feature_tts_google"
                                    type="checkbox"
                                    {{ $app_is_demo ? 'disabled' : '' }}
                                    {{ $settings_two->feature_tts_google ? 'checked' : '' }}
                            >
                            <span class="form-check-label">{{ __('Google') }}</span>
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('GCS File (JSON) path') }}</label>
                        <input
                                class="form-control"
                                id="gcs_file"
                                type="text"
                                name="gcs_file"
                                {{ $app_is_demo ? 'disabled' : '' }}
                                placeholder="googlefile.json"
                                value="{{ $app_is_demo ? '******************' : $setting->gcs_file }}"
                        >
                        <x-alert class="mt-2">
                            <p>
                                {{ __('Please upload your file to the /public_html/storage folder within your project and provide the file name in the space provided.') }}
                            </p>
                        </x-alert>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('GCS Project Name') }}</label>
                        <input
                                class="form-control"
                                id="gcs_name"
                                type="text"
                                name="gcs_name"
                                {{ $app_is_demo ? 'disabled' : '' }}
                                placeholder="{{ __('my-project-123') }}"
                                value="{{ $app_is_demo ? '******************' : $setting->gcs_name }}"
                        >
                    </div>

                </x-card>
                <x-card
                        class="mb-3 w-full"
                        size="sm"
                >
                    <div>
                        <label class="form-check form-switch">
                            <input
                                    class="form-check-input"
                                    id="feature_tts_openai"
                                    type="checkbox"
                                    {{ $app_is_demo ? 'disabled' : '' }}
                                    {{ $settings_two->feature_tts_openai ? 'checked' : '' }}
                            >
                            <span class="form-check-label">{{ __('OpenAI') }}</span>
                        </label>
                    </div>
                </x-card>
                <x-card
                        class="mb-3 w-full"
                        size="sm"
                >
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input
                                    class="form-check-input"
                                    id="feature_tts_elevenlabs"
                                    type="checkbox"
                                    {{ $app_is_demo ? 'disabled' : '' }}
                                    {{ $settings_two->feature_tts_elevenlabs ? 'checked' : '' }}
                            >
                            <span class="form-check-label">{{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->label() }}</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __(':label API Key', ['label' => \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->label()]) }}</label>
                        <input
                                class="form-control"
                                id="elevenlabs_api_key"
                                type="text"
                                name="elevenlabs_api_key"
                                placeholder="{{\App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->label()}} API Key"
                                {{ $app_is_demo ? 'disabled' : '' }}
                                value="{{ $app_is_demo ? '******************' : $settings_two->elevenlabs_api_key }}"
                        >
                    </div>
                </x-card>

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
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
