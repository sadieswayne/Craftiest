@use(\App\Domains\Entity\Enums\EntityEnum)
@extends('panel.layout.settings', ['layout' => 'wide'])
@section('title', __('XAI Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('This API key is used for all AI-powered features, including AI Chat, Image Generation, and Content Writing'))

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
        onsubmit="return xaiSettingsSave();"
        enctype="multipart/form-data"
    >
        <h3 class="mb-[25px] text-[20px]">{{ __('XAI Settings') }}</h3>
        <div class="row">
            @if ($app_is_demo)
                <div class="col-md-12">
                    <div class="mb-3">
                        <x-card
                            class="w-full"
                            size="sm"
                        >
                            <label class="form-label">{{ __('XAI API Secret') }}</label>
                            <input
                                class="form-control"
                                id="xai_api_secret"
                                type="text"
                                name="xai_api_secret"
                                value="*********************"
                            >
                        </x-card>
                    </div>
                </div>
            @else
                <div class="col-md-12">
                    <div class="mb-3">
                        <x-card
                            class="w-full"
                            size="sm"
                        >
                            <div
                                class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                                <label class="form-label">{{ __('XAI API Secret') }}
									<x-alert class="ms-2">
										<x-button
											variant="link"
											href="https://console.x.ai"
											target="_blank"
										>
											{{ __('Get an API key') }}
										</x-button>
									</x-alert>
								</label>

                                <select
                                    class="form-control select2"
                                    id="xai_api_secret"
                                    name="xai_api_secret"
                                    multiple
                                >
                                    @foreach (explode(',', setting('xai_api_secret','')) ?? [] as $secret)
                                        <option
                                            value="{{ $secret }}"
                                            selected
                                        >{{ $secret }}</option>
                                    @endforeach
                                </select>

                                <x-alert class="mt-2">
                                    <p class="text-justify">
                                        {{ __('You can enter as much API KEY as you want. Click "Enter" after each api key.') }}
                                    </p>
                                </x-alert>
                                <x-alert class="mt-2">
                                    <p class="text-justify">
                                        {{ __('Please ensure that your XAI API key is fully functional and billing defined on your XAI account.') }}
                                    </p>
                                </x-alert>
                                <a
                                    class="btn btn-primary mb-2 mt-2 w-full"
                                    href="{{ route('dashboard.admin.settings.x-ai.test') }}"
                                    target="_blank"
                                >
                                    {{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
                                </a>
								<div class="mt-2">
									@php
										$xAiWordDrivers = \App\Domains\Entity\EntityStats::word()
											->filterByEngine(\App\Domains\Engine\Enums\EngineEnum::X_AI)
											->list();
										$current_xai_model = EntityEnum::fromSlug(setting('xai_default_model', EntityEnum::GROK_2_1212->slug()));
									@endphp
									<x-model-select-list-with-change-alert :listLabel="'XAI Default Word Model'" :listId="'xai_default_model'" currentModel="{{ $current_xai_model }}" :drivers="$xAiWordDrivers" />
								</div>
							</div>
                        </x-card>
                    </div>
                </div>
            @endif
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
