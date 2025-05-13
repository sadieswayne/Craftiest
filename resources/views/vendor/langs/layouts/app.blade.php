@extends('panel.layout.settings', ['layout' => 'fullwidth'])
@section('title', __('Manage Languages'))

@section('titlebar_actions')
    <div class="flex flex-wrap items-center gap-3">
        @if (!activeRoute('elseyyid.translations.lang'))
            <x-button
                href="{{ route('elseyyid.translations.lang.reinstall') }}"
                variant="ghost-shadow"
            >
                <x-tabler-rotate-clockwise class="size-5" />
                {{ __('Reinstall Language Files') }}
            </x-button>
        @else
            <x-button
                href="{{ route('elseyyid.translations.lang.reinstall') }}"
                variant="ghost-shadow"
                type="{{ $app_is_demo ? 'button' : 'submit' }}"
                onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : 'return handlePromptInput()' }}"
            >
                <x-tabler-plus class="size-5" />
                {{ 'New String' }}
            </x-button>

            <form
                class="relative hidden"
                id="new-string-form"
                action="{{ route('elseyyid.translations.lang.newString') }}"
                method="GET"
            >
                <input
                    class="bg-[#F1EDFF]"
                    id="new-string"
                    type="text"
                    name="newString"
                    placeholder="{{ __('Add new string. Ex. Hello') }}"
                >
            </form>

            <script>
                function handlePromptInput() {
                    var newString = prompt('Enter the new string');
                    if (newString) {
                        document.getElementById('new-string').value = newString;
                        document.getElementById("new-string-form").submit();
                    }
                }
            </script>
        @endif

        <x-button
            href="{{ $app_is_demo ? 'javascript:void(0);' : route('elseyyid.translations.lang.publishAll') }}"
            onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        >
            <x-tabler-table-share class="size-5" />
            {{ __('Publish All JSON Files') }}
        </x-button>
    </div>
@endsection

@section('settings')
    @include('langs::includes.nav')
    @include('langs::includes.messages')

    @yield('content_translation')
@endsection

@push('script')
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <link
        href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
        rel="stylesheet"
    />
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script>
        $(document).ready(function() {
            "use strict";
            var local = "{{ LaravelLocalization::getCurrentLocale() }}";

            $('.lang-switcher .form-check-input').click(function() {
                var demo = @json($app_is_demo ? true : false);
                if (demo == true) {
                    toastr.info("{{ __('This feature is disabled in Demo version') }}");
                    return false;
                }
                var formData = new FormData();
                formData.append('lang', $(this).attr('id'));
                formData.append('state', $(this).prop('checked') ? 1 : 0);

                $.ajax({
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    url: "/translations/lang-save",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        toastr.success("{{ __('Saved. Redirecting...') }}");
                        setTimeout(function() {
                            location.href = "/" + local +
                                '/dashboard/admin/translations/home';
                        }, 1000);
                    },
                    error: function(data) {
                        var err = data.responseJSON.errors;
                        $.each(err, function(index, value) {
                            toastr.error(value);
                        });
                    }
                });

            });
        });
    </script>
    @yield('scripts')
@endpush
