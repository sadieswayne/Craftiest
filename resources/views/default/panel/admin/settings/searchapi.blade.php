@extends('panel.layout.settings', ['layout' => 'wide'])
@section('title', __('SearchApi Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('This API key is used for these features: AI Youtube'))

@section('additional_css')
    <link
        href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
        rel="stylesheet"
    />
@endsection

@section('settings')
    <form
        method="post"
        action="{{ route('dashboard.admin.settings.searchapi') }}"
        id="settings_form"
        enctype="multipart/form-data"
    >
        @csrf
        <div class="row">
            <!-- TODO OPENAI API KEY -->
            <x-card
                class="mb-3 max-md:text-center"
                szie="lg"
            >
                <div class="col-md-12">
                    <div
                        class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
                        <label class="form-label">{{ __('Search API key') }}</label>

						<input
							class="form-control"
							id="searchapi_api_key"
							type="text"
							name="searchapi_api_key"
							value="{{ setting("searchapi_api_key") }}"
							required
						>
                        <x-alert class="mt-2">
                            <p>
                                {{ __('You can enter as much API key as you want. Click "Enter" after each API key.') }}
                            </p>
                        </x-alert>
                        <x-alert class="mt-2">
                            <p>
                                {{ __('Please ensure that your Search API key is fully functional and billing defined on your Search API account.') }}
								<a target="_blank" href="https://www.searchapi.io/">Enter Site</a>
                            </p>
                        </x-alert>
						<div class="col-md-12 mt-3">
							<x-forms.input
								tooltip="{{ __('Active for AI Youtube') }}"
								class:container="mb-2"
								id="searchapi_api_for_youtube"
								type="checkbox"
								name="searchapi_api_for_youtube"
								:checked="setting('searchapi_api_for_youtube') == 1"
								label="{{ __('Active for AI Youtube') }}"
								switcher
							/>
						</div>
                    </div>
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
