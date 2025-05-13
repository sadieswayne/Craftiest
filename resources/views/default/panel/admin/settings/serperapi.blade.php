@extends('panel.layout.settings')
@section('title', __('Serper API Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('This API key is used for these features: AI Chat, AI Web Chat, Realtime Chat'))

@section('additional_css')
    <link
            href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
            rel="stylesheet"
    />
@endsection

@section('settings')
    <form
            id="settings_form"
            onsubmit="return serperSettingsSave();"
            enctype="multipart/form-data"
    >
        <x-card
                class="mb-2 max-md:text-center"
                szie="lg"
        >
            <!-- TODO Serper api key -->
            @if ($app_is_demo)
                <div class="mb-3">
                    <label class="form-label">{{ __(':label API Key', ['label' => \App\Domains\Engine\Enums\EngineEnum::SERPER->label()]) }}</label>
                    <input
                            class="form-control"
                            id="serper_api_key"
                            type="text"
                            name="serper_api_key"
                            value="*********************"
                    >
                </div>
            @else
                <div
                        class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                    <label class="form-label">{{ __(':label API Key', ['label' => \App\Domains\Engine\Enums\EngineEnum::SERPER->label()]) }}
                        <x-alert class="mt-2">
                            <x-button
                                    variant="link"
                                    href="https://serper.dev"
                                    target="_blank"
                            >
                                {{ __('Get an API key') }}
                            </x-button>
                        </x-alert>
                    </label>
                    <input
                            class="form-control"
                            id="serper_api_key"
                            type="text"
                            name="serper_api_key"
                            value="{{ $settings_two->serper_api_key }}"
                            required
                    >
                    <x-alert class="mt-2">
                        <p>
                            {{ __('Please ensure that your Serper api key is fully functional and billing defined on your Serper account.') }}
                        </p>
                    </x-alert>
                    <a
                            class="btn btn-primary mb-2 mt-2 w-full"
                            href="{{ route('dashboard.admin.settings.serperapi.test') }}"
                            target="_blank"
                    >
                        {{ __('After Saving Setting, Click Here to Test Your api key') }}
                    </a>
                </div>
            @endif
        </x-card>

        @includeFirst(['seo-tool::particles.serper-seo', 'panel.admin.settings.particles.serper_seo', 'vendor.empty'])

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
