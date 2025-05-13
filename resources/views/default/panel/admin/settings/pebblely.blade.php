@extends('panel.layout.settings')
@section('title', __(\App\Domains\Engine\Enums\EngineEnum::PEBBLELY->label() . ' Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('This API key is used for these features: AI Product Photography'))

@section('additional_css')
    <link
            href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
            rel="stylesheet"
    />
@endsection

@section('settings')
    <form
            id="settings_form"
            onsubmit="return pebblelySettingsSave();"
            enctype="multipart/form-data"
    >
        <x-card
                class="mb-2 max-md:text-center"
                szie="lg"
        >

            @if ($app_is_demo)
                <div class="mb-3">
                    <label class="form-label">{{ __(':label API Key', ['label' => \App\Domains\Engine\Enums\EngineEnum::PEBBLELY->label()]) }}</label>
                    <input
                            class="form-control"
                            id="pebblely_key"
                            type="text"
                            name="pebblely_key"
                            value="*********************"
                    >
                </div>
            @else
                <div
                        class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                    <label class="form-label">{{ __(':label API Key', ['label' => \App\Domains\Engine\Enums\EngineEnum::PEBBLELY->label()]) }}
                        <x-alert class="mt-2">
                            <x-button
                                    variant="link"
                                    href="https://pebblely.com/docs/"
                                    target="_blank"
                            >
                                {{ __('Get an API key') }}
                            </x-button>
                        </x-alert>
                    </label>
                    <input
                            class="form-control"
                            id="pebblely_key"
                            type="text"
                            name="pebblely_key"
                            value="{{ $setting->pebblely_key }}"
                    >
                    <x-alert
                            class="mt-2"
                            variant="lg"
                    >
                        <p>
                            {{ __('Please ensure that your :label api key is fully functional and billing defined on your :label account.', ['label' => \App\Domains\Engine\Enums\EngineEnum::PEBBLELY->label()]) }}
                        </p>
                    </x-alert>
                </div>
            @endif

        </x-card>
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
