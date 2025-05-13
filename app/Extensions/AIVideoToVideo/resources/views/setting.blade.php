@php use App\Domains\Entity\Enums\EntityEnum; @endphp
@extends('panel.layout.settings', ['layout' => 'wide'])
@section('title', __('FalAI Settings'))
@section('titlebar_actions', '')
@section('additional_css')
    <link
        href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
        rel="stylesheet"
    />
@endsection

@section('settings')
    <form
        method="post"
        action="{{ route('dashboard.admin.settings.fal-ai') }}"
        id="settings_form"
        enctype="multipart/form-data"
    >
        @csrf
        <h3 class="mb-[25px] text-[20px]">{{ __('FalAI Settings') }}</h3>
        <div class="row">
            <!-- TODO OPENAI API KEY -->
            <x-card
                class="mb-3 max-md:text-center"
                szie="lg"
            >

                <div class="col-md-12">
                    <div
                        class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                        <label class="form-label">{{ __('FalAI API key') }}</label>

                        <select
                            class="form-control select2"
                            id="fal_ai_api_secret"
                            name="fal_ai_api_secret"
                            multiple
                        >
                            @if($app_is_demo)
                                <option selected value="*********************">*********************</option>
                            @else
                                @foreach (explode(',', setting('fal_ai_api_secret')) as $secret)
                                    <option
                                        value="{{ $secret }}"
                                        selected
                                    >{{ $secret }}</option>
                                @endforeach
                            @endif
                        </select>
                        <x-alert class="mt-2">
                            <p>
                                {{ __('You can enter as much API key as you want. Click "Enter" after each API key.') }}
                            </p>
                        </x-alert>
                        <x-alert class="mt-2">
                            <p>
                                {{ __('Please ensure that your FalAI API key is fully functional and billing defined on your FalAI account.') }}
                            </p>
                        </x-alert>
                    </div>
                </div>
            </x-card>
            <x-card
                class="mb-3 max-md:text-center"
                szie="lg"
            >
                <div class="mb-3">
					@php
						$fluxDrivers = \App\Domains\Entity\EntityStats::image()
							->filterByEngine(\App\Domains\Engine\Enums\EngineEnum::FAL_AI)
							->list();
						$current_flux_model = EntityEnum::fromSlug(setting('fal_ai_default_model', EntityEnum::FLUX_PRO->slug()))->slug();
					@endphp
					<x-model-select-list-with-change-alert :listLabel="'Default Flux Image Model'" :listId="'fal_ai_default_model'" currentModel="{{ $current_flux_model }}" :drivers="$fluxDrivers" />
                </div>
            </x-card>
        </div>
        <button
            class="btn btn-primary w-full"
            type="submit"
        >
            {{ __('Save') }}
        </button>
    </form>
@endsection
@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
