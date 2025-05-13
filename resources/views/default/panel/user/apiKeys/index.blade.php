@php
use App\Domains\Engine\Enums\EngineEnum;
@endphp
@extends('panel.layout.app')
@section('title', __('API Keys'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('Integrate your own personal OpenAI API Key and generate AI content.'))
@section('additional_css')
    <link
            href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
            rel="stylesheet"
    />
@endsection
@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <form
                            class="@if (view()->exists('panel.admin.custom.layout.panel.header-top-bar')) bg-[--tblr-bg-surface] px-8 py-10 rounded-[--tblr-border-radius] @endif"
                            id="settings_form"
                            onsubmit="return apiKeysSettingsSave();"
                            enctype="multipart/form-data"
                    >
                        <h3 class="mb-[25px] text-[20px]">{{ __('Api Keys Setting') }}</h3>
                        @switch(setting('default_ai_engine', 'openai'))
                            @case(EngineEnum::OPEN_AI->value)
                                <div class="row">
                                    @if ($app_is_demo)
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('OpenAI Api Keys Secret') }}</label>
                                                <input
                                                        class="form-control"
                                                        id="openai_api_secret"
                                                        type="text"
                                                        name="openai_api_secret"
                                                        readonly
                                                        placeholder="Paste your API Key here. Need help? Learn more about API Keys"
                                                >
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-12">
                                            <div
                                                    class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                                                <label class="form-label">{{ __('OpenAI API Secret') }}</label>
												<select
													class="form-control select2"
													id="openai_api_keys"
													name="openai_api_keys"
													multiple
												>
													@foreach (explode(',', $list) ?? [] as $secret)
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
                                                        {{ __('Please ensure that your OpenAI API key is fully functional and billing defined on your OpenAI account.') }}
                                                    </p>
                                                </x-alert>
                                                <a
                                                        class="btn btn-primary mb-2 mt-2 w-full"
                                                        href="{{ route('dashboard.user.settings.openai.test') }}"
                                                        target="_blank"
                                                >
                                                    {{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @break

                            @case(EngineEnum::ANTHROPIC->value)
                                <div class="row">
                                    @if ($app_is_demo)
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __(EngineEnum::ANTHROPIC->label().' Api Keys Secret') }}</label>
                                                <input
                                                        class="form-control"
                                                        id="anthropic_api_secret"
                                                        type="text"
                                                        name="anthropic_api_secret"
                                                        readonly
                                                        placeholder="Paste your API Key here. Need help? Learn more about API Keys"
                                                >
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-12">
                                            <div
                                                    class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                                                <label class="form-label">{{ __('Anthropic API Secret') }}</label>
                                                <select
                                                        class="form-control select2"
                                                        id="anthropic_api_keys"
                                                        name="anthropic_api_keys"
                                                        multiple
                                                >
                                                    @foreach (explode(',', $anthropic_api_keys) ?? [] as $secret)
                                                        <option
                                                                value="{{ $secret }}"
                                                                @if ($secret) selected @endif
                                                        >{{ $secret }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <x-alert class="mt-2">
                                                    <p>
                                                        {{ __('You can enter as much API KEY as you want. Click "Enter" after each api key.') }}
                                                    </p>
                                                </x-alert>
                                                <x-alert class="mt-2">
                                                    <p>
                                                        {{ __('Please ensure that your Anthropic API key is fully functional and billing defined on your Anthropic account.') }}
                                                    </p>
                                                </x-alert>
                                                <a
                                                        class="btn btn-primary mb-2 mt-2 w-full"
                                                        href="{{ route('dashboard.user.settings.anthropic.test') }}"
                                                        target="_blank"
                                                >
                                                    {{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @break

                            @case(EngineEnum::GEMINI->value)
                                <div class="row">
                                    @if ($app_is_demo)
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __(EngineEnum::GEMINI->label().' Api Keys Secret') }}</label>
                                                <input
                                                        class="form-control"
                                                        id="gemini_api_keys"
                                                        type="text"
                                                        name="gemini_api_keys"
                                                        readonly
                                                        placeholder="Paste your API Key here. Need help? Learn more about API Keys"
                                                >
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-12">
                                            <div
                                                    class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                                                <label class="form-label">{{ __(EngineEnum::GEMINI->label().' API Secret') }}</label>
                                                <select
                                                        class="form-control select2"
                                                        id="gemini_api_keys"
                                                        name="gemini_api_keys"
                                                        multiple
                                                >
                                                    @foreach (explode(',', $gemini_api_keys) ?? [] as $secret)
                                                        <option
                                                                value="{{ $secret }}"
                                                                @if ($secret) selected @endif
                                                        >{{ $secret }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <x-alert class="mt-2">
                                                    <p>
                                                        {{ __('You can enter as much API KEY as you want. Click "Enter" after each api key.') }}
                                                    </p>
                                                </x-alert>
                                                <x-alert class="mt-2">
                                                    <p>
                                                        {{ __('Please ensure that your Anthropic API key is fully functional and billing defined on your Anthropic account.') }}
                                                    </p>
                                                </x-alert>
                                                <a
                                                        class="btn btn-primary mb-2 mt-2 w-full"
                                                        href="{{ route('dashboard.user.settings.gemini.test') }}"
                                                        target="_blank"
                                                >
                                                    {{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @break
                            @default
                                <div class="row">
                                    @if ($app_is_demo)
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('OpenAI Api Keys Secret') }}</label>
                                                <input
                                                        class="form-control"
                                                        id="openai_api_secret"
                                                        type="text"
                                                        name="openai_api_secret"
                                                        readonly
                                                        placeholder="Paste your API Key here. Need help? Learn more about API Keys"
                                                >
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-12">
                                            <div
                                                    class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                                                <label class="form-label">{{ __('OpenAI API Secret') }}</label>
                                                <select
                                                        class="form-control select2"
                                                        id="openai_api_keys"
                                                        name="openai_api_keys"
                                                        multiple
                                                >
                                                    @foreach (explode(',', $list) ?? [] as $secret)
                                                        <option
                                                                value="{{ $secret }}"
                                                                @if ($secret) selected @endif
                                                        >{{ $secret }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <x-alert class="mt-2">
                                                    <p>
                                                        {{ __('You can enter as much API KEY as you want. Click "Enter" after each api key.') }}
                                                    </p>
                                                </x-alert>
                                                <x-alert class="mt-2">
                                                    <p>
                                                        {{ __('Please ensure that your OpenAI API key is fully functional and billing defined on your OpenAI account.') }}
                                                    </p>
                                                </x-alert>
                                                <a
                                                        class="btn btn-primary mb-2 mt-2 w-full"
                                                        href="{{ route('dashboard.user.settings.openai.test') }}"
                                                        target="_blank"
                                                >
                                                    {{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                        @endswitch
                        <button
                                class="btn btn-primary mt-5 w-full"
                                id="settings_button"
                                form="settings_form"
                        >
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
