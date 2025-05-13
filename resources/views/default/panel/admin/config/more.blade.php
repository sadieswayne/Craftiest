@extends('panel.layout.settings')
@section('title', __('More Settings'))
@section('titlebar_actions', '')
@section('additional_css')

@endsection

@section('settings')
    <form id="configForm" action="{{route("dashboard.admin.config.more.store")}}" method="POST">
        @csrf
        <x-form-step
                class="mb-4 mt-5"
                step="1"
                label="{{ __('More Settings') }}"
        />
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('User Onboarding') }}
                        <x-badge
                                class="ms-2 text-2xs"
                                variant="secondary"
                        >
                            @lang('New')
                        </x-badge>
                    </label>
                    <select
                            class="form-select"
                            id="tour_seen"
                            name="tour_seen"
                    >
                        <option
                                value="1"
                                {{ $setting->tour_seen == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                                value="0"
                                {{ $setting->tour_seen == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Realtime Chat Model') }}
                        <x-badge
                            class="ms-2 text-2xs"
                            variant="secondary"
                        >
                            @lang('New')
                        </x-badge>
                    </label>
                    <select
                        class="form-select"
                        id="default_realtime"
                        name="default_realtime"
                    >
                        <option
                            value="serper"
                            {{ setting("default_realtime") === "serper" ? 'selected' : '' }}
                        >
                            {{ __('Serper') }}</option>
						@includeIf('perplexity::select-option')
                    </select>
                </div>
            </div>

            <x-form-step
                    class="mb-4 mt-5"
                    step="2"
                    label="{{ __('Advanced Settings') }}"
            />
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">
                            {{ __('Code before </head> (Dashboard)') }}
                            <x-info-tooltip
                                    text="{{ __('Only accepts javascript code wrapped with <script> tags and HTML markup that is valid inside the </head> tag.') }}"/>
                        </label>
                        <textarea
                                class="form-control"
                                id="dashboard_code_before_head"
                                name="dashboard_code_before_head"
                        >{{ $setting->dashboard_code_before_head }}</textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">
                            {{ __('Code before </body> (Dashboard)') }}
                            <x-info-tooltip
                                    text="{{ __('Only accepts javascript code wrapped with <script> tags and HTML markup that is valid inside the </body> tag.') }}"/>
                        </label>
                        <textarea
                                class="form-control"
                                id="dashboard_code_before_body"
                                name="dashboard_code_before_body"
                        >{{ $setting->dashboard_code_before_body }}</textarea>
                    </div>
                </div>
            </div>

            <x-form-step
                    class="mb-4 mt-5"
                    step="3"
                    label="{{ __('Mobile Settings') }}"
            />
            <div class="row mb-4">
                <div class="mb-3">
                    <div class="form-label">
                        {{ __('These settings are for the mobile app. You can ask for a demo to our representatives.') }}
                    </div>
                    <x-forms.input
                            id="mobile_payment_active"
                            name="mobile_payment_active"
                            type="checkbox"
                            :checked="$setting?->mobile_payment_active == 1"
                            switcher
                            label="{{ __('Mobile Payment') }}"
                    />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="mb-4">
                        <label class="form-label">{{ __('Mr Robot Name') }}</label>
                        <input
                                class="form-control"
                                id="mrrobot_name"
                                type="text"
                                name="mrrobot_name"
                                placeholder="{{ __('Mr Robot') }}"
                                value="{{ $setting->mrrobot_name }}"
                        >
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Mr Robot Search Words') }}
                            <x-info-tooltip
                                    text="{{ __('These words will be used as search default values of the Mr Robot. Separate your content with comma.') }}"/>
                        </label>
                        <textarea
                                class="form-control"
                                id="mrrobot_search_words"
                                name="mrrobot_search_words"
                                placeholder="{{ __('Product Description,Interior Designer') }}"
                                rows="3"
                        >{{ $setting->mrrobot_search_words }}</textarea>
                    </div>
                </div>
            </div>
            <x-form-step
                    class="mb-4 mt-5"
                    step="4"
                    label="{{ __('Notification Settings') }}"
            >
            </x-form-step>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="mb-3">
                        <x-forms.input
                                id="notification_active"
                                name="notification_active"
                                type="checkbox"
                                switcher
                                type="checkbox"
                                :checked="setting('notification_active', 0) == 1"
                                label="{{ __('Activate Notifications System') }}"
                                tooltip="{{ __('To use the notification system, you must activate it and use Pusher.') }}"
                        />
                    </div>

                    <x-card class="mb-3">
                        <h4 class="mb-3">{{ __('Pusher Settings') }}</h4>
                        <x-alert class="rounde mb-4">
                            <a
                                    href="https://magicaidocs.liquid-themes.com/pusher-configuration"
                                    target="_blank"
                            >
                                {{ __('Check the documentation.') }}
                                <x-tabler-arrow-up-right class="size-4 inline align-text-bottom"/>
                            </a>
                        </x-alert>
                        <x-forms.input
                                class:container="mb-3"
                                id="pusher_app_id"
                                type="text"
                                size="lg"
                                name="pusher_app_id"
                                value="{{ setting('pusher_app_id') }}"
                                label="{{ __('App ID') }}"
                        />
                        <x-forms.input
                                class:container="mb-3"
                                id="pusher_app_key"
                                type="text"
                                size="lg"
                                name="pusher_app_key"
                                value="{{ setting('pusher_app_key') }}"
                                label="{{ __('App Key') }}"
                        />
                        <x-forms.input
                                class:container="mb-3"
                                id="pusher_app_secret"
                                type="text"
                                size="lg"
                                name="pusher_app_secret"
                                value="{{ setting('pusher_app_secret') }}"
                                label="{{ __('App Secret') }}"
                        />
                        <x-forms.input
                                class:container="mb-3"
                                id="pusher_app_cluster"
                                type="text"
                                size="lg"
                                name="pusher_app_cluster"
                                value="{{ setting('pusher_app_cluster', 'mt1') }}"
                                label="{{ __('Cluster') }}"
                        />
                    </x-card>
                </div>
            </div>

            <div class="col-12">
                <button
                        class="btn btn-primary w-full"
                        type="submit"
                >
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js?v=' . time()) }} }}"></script>
    <script
            src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"
            type="text/javascript"
            charset="utf-8"
    ></script>
    <script src="{{ custom_theme_url('https://foliotek.github.io/Croppie/croppie.js') }}"></script>
    <style
            type="text/css"
            media="screen"
    >
        .ace_editor {
            min-height: 200px;
        }
    </style>

    <script>
		const dashboardCodeBeforeHead = ace.edit('dashboard_code_before_head');
		dashboardCodeBeforeHead.session.setMode("ace/mode/html");

        const dashboardCodeBeforeBody = ace.edit("dashboard_code_before_body");
        dashboardCodeBeforeBody.session.setMode("ace/mode/html");

        document.getElementById('configForm').addEventListener('submit', function (event) {
            event.preventDefault();
			const formData = new FormData(this);
			formData.set('dashboard_code_before_head', dashboardCodeBeforeHead.getValue());
            formData.set('dashboard_code_before_body', dashboardCodeBeforeBody.getValue());
			$.ajax({
                type: "POST",
                url: "{{ route('dashboard.admin.config.more.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    toastr.success('Settings saved successfully.');
                    location.reload();
                },
                error: function (xhr) {
                    toastr.error('An error occurred while saving settings.');
                }
            });
        });
    </script>

@endpush
