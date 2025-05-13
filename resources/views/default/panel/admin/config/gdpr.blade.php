@extends('panel.layout.settings')
@section('title', __('GDPR Settings'))
@section('titlebar_actions', '')

@section('settings')
    <form action="{{route("dashboard.admin.config.gdpr.store")}}" method="POST">
        @csrf
        <div class="row">

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input
                                class="form-check-input"
                                id="gdpr_status"
                                type="checkbox"
                                name="gdpr_status"
                                {{ $setting->gdpr_status ? 'checked' : '' }}
                        >
                        <span class="form-check-label">{{ __('Enable GDPR Alert Box') }}</span>
                    </label>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Button') }}
                        <x-info-tooltip text="{{ __('Accept button text') }}"/>
                    </label>
                    <input
                            class="form-control"
                            id="gdpr_button"
                            type="text"
                            name="gdpr_button"
                            value="{{ $setting->gdpr_button }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Content') }}
                        <x-info-tooltip text="{{ __('GDPR alert text. You can use HTML tags.') }}"/>
                    </label>
                    <textarea
                            class="form-control"
                            id="gdpr_content"
                            name="gdpr_content"
                    >{{ $setting->gdpr_content }}</textarea>
                </div>
            </div>

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
@endpush
