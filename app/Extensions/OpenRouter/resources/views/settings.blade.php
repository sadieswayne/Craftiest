@php use App\Domains\Entity\Enums\EntityEnum; @endphp
@extends('panel.layout.settings')
@section('title', __('OpenRouter Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('This API key is used for these features: AI Writer, AI Chat'))

@section('additional_css')
    <link
        href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
        rel="stylesheet"
    />
@endsection

@section('settings')
    <form
        id="settings_form"
        action="{{route('dashboard.admin.settings.open-router.update')}}"
        method="POST"
    >
        @csrf
        @method('PUT')
        <x-card
            class="mb-2 max-md:text-center"
            szie="lg"
        >

            @if ($app_is_demo)
                <div class="mb-3">
                    <label class="form-label">{{ __('OpenRouter API Key') }}</label>
                    <input
                        class="form-control"
                        id=""
                        type="text"
                        name="open_router_api"
                        value="*********************"
                    >
                </div>
            @else
                <div
                    class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                    <label class="form-label">{{ __('OpenRouter API Key') }}
                        <x-alert class="mt-2">
                            <x-button
                                variant="link"
                                href="https://openrouter.ai/"
                                target="_blank"
                            >
                                {{ __('Get an API key') }}
                            </x-button>
                        </x-alert>
                    </label>
                    <input
                        class="form-control"
                        id="open_router_api"
                        type="text"
                        name="open_router_api"
                        value="{{ setting('open_router_api')}}"
                    >
                    <x-alert
                        class="mt-2"
                        variant="lg"
                    >
                        <p>
                            {{ __('Please ensure that your OpenRouter API key is fully functional and billing defined on your OpenRouter account.') }}
                        </p>
                    </x-alert>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
						@php
							$drivers = \App\Domains\Entity\EntityStats::word()
								->filterByEngine(\App\Domains\Engine\Enums\EngineEnum::OPEN_ROUTER)
								->list();
							$current_model = EntityEnum::fromSlug(setting('default_open_router_model', EntityEnum::EVA_QWEN25_14B->slug()))->slug();
						@endphp
						<x-model-select-list-with-change-alert :listLabel="'Default OpenRouter Model'" :listId="'default_open_router_model'" currentModel="{{ $current_model }}" :drivers="$drivers" />
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <x-card
                            class="w-full"
                            size="sm"
                        >
                            <label class="form-label">{{ __('Open Router Status') }}</label>
                            <select
                                class="form-select"
                                id="open_router_status"
                                name="open_router_status"
                            >
                                <option value="0"
                                    {{ setting('open_router_status') == '0' ? 'selected' : null }}>
                                    {{ __('Passive') }}
                                </option>
                                <option value="1"
                                    {{ setting('open_router_status') == '1' ? 'selected' : null }}>
                                    {{ __('Active') }}
                                </option>
                            </select>
                        </x-card>
                    </div>
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
