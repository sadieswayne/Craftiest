@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('AI Article Wizard'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('Just choose your topic, and watch AI whip up SEO-optimized blog content in a matter of seconds!'))
@section('titlebar_actions_before')
    <div class="flex w-full flex-wrap items-center gap-6 lg:justify-end">
        <x-credit-list />
        <x-button
            class="reset-wizard-btn group-[:not([data-step='1'],[data-step='2'],[data-step='3'])]/article-wizard:hidden"
            variant="outline"
            onclick="resetWizard()"
        >
            <x-tabler-refresh class="size-4" />
            {{ __('Start Over') }}
        </x-button>

    </div>
@endsection

@section('content')
    <div class="py-10">
        @include('panel.user.article_wizard.components.wizard_settings')
    </div>
    <input
        id="guest_id"
        type="hidden"
        value="{{ $apiUrl }}"
    >
    <input
        id="guest_event_id"
        type="hidden"
        value="{{ $apikeyPart1 }}"
    >
    <input
        id="guest_look_id"
        type="hidden"
        value="{{ $apikeyPart2 }}"
    >
    <input
        id="guest_product_id"
        type="hidden"
        value="{{ $apikeyPart3 }}"
    >
@endsection

@push('script')
    <script>
        function resetWizard() {
            $.ajax({
                url: '{{ route('dashboard.user.openai.articlewizard.startover') }}',
                type: 'POST',
            }).done(function(response) {
                window.location.reload();
            });
        }
    </script>
    <script src="{{ custom_theme_url('/assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        let stream_type = '{!! $settings_two->openai_default_stream_server !!}';
        const openai_model = '{{ $setting->openai_default_model }}';
        const guest_id = document.getElementById("guest_id")?.value;
        const guest_event_id = document.getElementById("guest_event_id")?.value;
        const guest_look_id = document.getElementById("guest_look_id")?.value;
        const guest_product_id = document.getElementById("guest_product_id")?.value;
    </script>
    <script src="{{ custom_theme_url('/assets/js/panel/article_wizard.js?v=' . time()) }}"></script>
    <script>
        let selected_step = -1;
        @if (isset($wizard))
            CUR_STATE = {
                ...@json($wizard)
            };
            selected_step = CUR_STATE.current_step;
            image_storage = @json($settings_two->ai_image_storage);
            updateData();
        @endif
    </script>
@endpush
