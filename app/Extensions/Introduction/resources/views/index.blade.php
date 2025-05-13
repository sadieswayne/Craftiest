@extends('panel.layout.settings')
@section('title', __('User Onboarding'))
@section('titlebar_actions', '')

@section('settings')
    <form
        id="settings_form"
        method="POST"
        action="{{ route('dashboard.introductions.store') }}"
        onsubmit="return introductionSettingsSave();"
        enctype="multipart/form-data"
    >
        @csrf
        <h3 class="mb-[25px] text-[20px]">{{ __('User Onboarding Settings') }}</h3>

        @foreach ($list as $item)
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ \App\Enums\Introduction::getLabel($item['key']) }}</label>
                    <input
                        class="form-control"
                        id="{{ $item['key'] }}"
                        type="text"
                        name="{{ $item['key'] }}"
                        value="{{ $item['intro'] }}"
                    >
                </div>
            </div>
        @endforeach
        <button
            class="btn btn-primary w-full"
            id="settings_button"
        >
            {{ __('Save') }}
        </button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
@endpush
