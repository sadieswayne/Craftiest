@php use App\Domains\Entity\Enums\EntityEnum; @endphp
@extends('panel.layout.settings', ['layout' => 'wide'])
@section('title', __('Fal ai Settings'))
@section('titlebar_actions', '')
@section('additional_css')
    <link
            href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
            rel="stylesheet"
    />
    <style>

    </style>
@endsection

@section('settings')
    <form
            id="settings_form"
            onsubmit="return falAiSettingsSave();"
            enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('Anthropic Settings') }}</h3>
        <div class="row">
            <!-- TODO OPENAI API KEY -->
            @if ($app_is_demo)
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __(\App\Domains\Engine\Enums\EngineEnum::FAL_AI->label() .' API Secret') }}</label>
                        <input
                                class="form-control"
                                id="fal_ai_api_secret"
                                type="text"
                                name="fal_ai_api_secret"
                                value="*********************"
                        >
                    </div>
                </div>
            @else
                <div class="col-md-12">
                    <div
                            class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                        <label class="form-label">{{ __(\App\Domains\Engine\Enums\EngineEnum::FAL_AI->label() .' API key') }}</label>

                        <select
                                class="form-control select2"
                                id="fal_ai_api_secret"
                                name="fal_ai_api_secret"
                                multiple
                        >
                            @foreach (explode(',', setting('fal_ai_api_secret')) as $secret)
                                <option
                                        value="{{ $secret }}"
                                        selected
                                >{{ $secret }}</option>
                            @endforeach
                        </select>

                        <x-alert class="mt-2">
                            <p>
                                {{ __('You can enter as much API KEY as you want. Click "Enter" after each api key.') }}
                            </p>
                        </x-alert>
                        <x-alert class="mt-2">
                            <p>
                                {{ __('Please ensure that your Fal AI API key is fully functional and billing defined on your Anthropic account.') }}
                            </p>
                        </x-alert>
                    </div>
                </div>
            @endif

            <input hidden name="fal_ai_default_model" id="fal_ai_default_model" value="{{ EntityEnum::FLUX_PRO->value }}">

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
    <script>
        function falAiSettingsSave() {
            "use strict";

            document.getElementById("settings_button").disabled = true;
            document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

            var formData = new FormData();
            formData.append('fal_ai_api_secret', $("#fal_ai_api_secret").val());
            formData.append('fal_ai_default_model', $("#fal_ai_default_model").val());

            $.ajax({
                type: "post",
                url: "/dashboard/admin/settings/fal-ai",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    toastr.success(magicai_localize?.settings_saved || 'Settings saved succesfully')
                    document.getElementById("settings_button").disabled = false;
                    document.getElementById("settings_button").innerHTML = "Save";
                },
                error: function (data) {
                    var err = data.responseJSON.errors;
                    $.each(err, function (index, value) {
                        toastr.error(value);
                    });
                    document.getElementById("settings_button").disabled = false;
                    document.getElementById("settings_button").innerHTML = "Save";
                }
            });
            return false;
        }

    </script>

    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
