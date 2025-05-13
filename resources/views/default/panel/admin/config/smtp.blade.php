@extends('panel.layout.settings', ['layout' => 'fullwidth'])
@section('title', __('SMTP Settings'))
@section('titlebar_actions', '')

@section('settings')
    <div class="row">
        <div class="col-md-5 mx-auto">
            <form action="{{route("dashboard.admin.config.smtp.store")}}" method="POST">
                @csrf
                <h3 class="mb-[25px] text-[20px]">{{ __('SMTP Settings') }}</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('SMTP Host') }}</label>
                            <input
                                    class="form-control"
                                    id="smtp_host"
                                    type="text"
                                    name="smtp_host"
                                    value="{{ $setting->smtp_host }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('SMTP Port') }}</label>
                            <input
                                    class="form-control"
                                    id="smtp_port"
                                    type="text"
                                    name="smtp_port"
                                    value="{{ $setting->smtp_port }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('SMTP Username') }}</label>
                            <input
                                    class="form-control"
                                    id="smtp_username"
                                    type="text"
                                    name="smtp_username"
                                    value="{{ $setting->smtp_username }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('SMTP Password') }}</label>
                            <input
                                    class="form-control"
                                    id="smtp_password"
                                    type="password"
                                    name="smtp_password"
                                    value="{{ $setting->smtp_password }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('SMTP Sender Email') }}</label>
                            <input
                                    class="form-control"
                                    id="smtp_email"
                                    type="text"
                                    name="smtp_email"
                                    value="{{ $setting->smtp_email }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('SMTP Sender Name') }}</label>
                            <input
                                    class="form-control"
                                    id="smtp_sender_name"
                                    type="text"
                                    name="smtp_sender_name"
                                    value="{{ $setting->smtp_email }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('SMTP Encryption') }}</label>
                            <input
                                    class="form-control"
                                    id="smtp_encryption"
                                    type="text"
                                    name="smtp_encryption"
                                    value="{{ $setting->smtp_encryption }}"
                            >
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
        </div>

        <div class="col-md-5 mx-auto">
            <form
                    id="smtp_test_form"
                    action="{{ route('dashboard.admin.config.smtp.test') }}"
                    method="POST"
            >
                @csrf
                <h3 class="mb-[25px] text-[20px]">{{ __('SMTP Test') }}</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Test Email') }}</label>
                            <input
                                    class="form-control"
                                    type="text"
                                    name="test_email"
                                    placeholder="Email to send test email."
                            >
                        </div>
                    </div>
                </div>
                <button
                        class="btn btn-primary w-full"
                        id="smtp_button"
                        form="smtp_test_form"
                >
                    {{ __('Save') }}
                </button>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
@endpush
