@extends('panel.layout.settings')
@section('title', __('Privacy Policy and Terms Settings'))
@section('titlebar_actions', '')

@section('settings')
    <form
        id="settings_form"
        onsubmit="return privacySettingsSave();"
        enctype="multipart/form-data"
    >
        <div class="row">

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input
                            class="form-check-input"
                            id="privacy_enable"
                            type="checkbox"
                            {{ $setting->privacy_enable ? 'checked' : '' }}
                        >
                        <span class="form-check-label">{{ __('Enable Privacy Policy and Terms') }}</span>
                    </label>
                </div>
                <div class="hide-page-element mb-3">
                    <label class="form-check form-switch">
                        <input
                            class="form-check-input"
                            id="privacy_enable_login"
                            type="checkbox"
                            {{ $setting->privacy_enable_login ? 'checked' : '' }}
                        >
                        <span class="form-check-label">{{ __('Show on the Login Page') }}</span>
                    </label>
                </div>
            </div>

            <h3 class="hide-page-element mt-4 inline-flex items-baseline gap-x-2 text-[20px]">
                {{ __('Privacy Policy') }}
                <a
                    class="text-[12px] text-gray-400"
                    target="_blank"
                    href="{{ url('/') }}/privacy-policy"
                >/privacy-policy
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="12"
                        height="12"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none"
                        ></path>
                        <path d="M17 7l-10 10"></path>
                        <path d="M8 7l9 0l0 9"></path>
                    </svg>
                </a>
            </h3>

            <div class="col-md-12 hide-page-element">
                <select
                    class="form-control mb-4 bg-[#F1EDFF]"
                    id="privacyLocal"
                    name="privacyLocal"
                    onchange="handleSelectChangeLang('privacy');"
                >
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @if (in_array($localeCode, explode(',', $settings_two->languages)))
                            <option
                                class="p-0"
                                value="{{ $localeCode }}"
                                @if ($settings_two->languages_default === $localeCode) {{ 'selected' }} @endif
                            >
                                <span class="!me-2 text-[21px]">{{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}</span>
                                {{ ucfirst($properties['native']) }} @if ($settings_two->languages_default === $localeCode)
                                @endif
                            </option>
                        @endif
                    @endforeach
                </select>
                <div class="mb-3">
                    <textarea
                        class="form-control"
                        id="privacy_content"
                        name="privacy_content"
                    >{{ $setting->privacy_content }}</textarea>
                </div>
            </div>

            <h3 class="hide-page-element mt-4 inline-flex items-baseline gap-x-2 text-[20px]">
                {{ __('Terms & Conditions') }}
                <a
                    class="text-[12px] text-gray-400"
                    target="_blank"
                    href="{{ url('/') }}/terms"
                >/terms
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="12"
                        height="12"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none"
                        ></path>
                        <path d="M17 7l-10 10"></path>
                        <path d="M8 7l9 0l0 9"></path>
                    </svg>
                </a>

            </h3>

            <div class="col-md-12 hide-page-element">
                <select
                    class="form-control mb-4 bg-[#F1EDFF]"
                    id="termsLocal"
                    name="termsLocal"
                    onchange="handleSelectChangeLang('terms');"
                >
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @if (in_array($localeCode, explode(',', $settings_two->languages)))
                            <option
                                class="p-0"
                                value="{{ $localeCode }}"
                                @if ($settings_two->languages_default === $localeCode) {{ 'selected' }} @endif
                            >
                                <span class="!me-2 text-[21px]">{{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}</span>
                                {{ ucfirst($properties['native']) }} @if ($settings_two->languages_default === $localeCode)
                                @endif
                            </option>
                        @endif
                    @endforeach
                </select>
                <div class="mb-3">
                    <textarea
                        class="form-control"
                        id="terms_content"
                        name="terms_content"
                    >{{ $setting->terms_content }}</textarea>
                </div>
            </div>

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
    <script src="{{ custom_theme_url('/assets/js/panel/tinymce-theme-handler.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: '#privacy_content,#terms_content',
            plugins: 'quickbars advlist link image lists',
            //toolbar:'advlist link image lists'
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | lists | indent outdent | image',
            quickbars_insert_toolbar: false,
            content_css: `${window.liquid.assetsPath}/css/tinymce-theme.css`,
            setup: function(editor) {
                liquidTinyMCEThemeHandlerInit(editor);
            }
        });
    </script>
    <script>
        (() => {
            checkPrivacy();
            // Checkbox değeri değiştiğinde kontrol et
            $("#privacy_enable").change(function() {
                checkPrivacy();
            });

            function checkPrivacy() {
                if ($("#privacy_enable").is(":checked")) {
                    $(".hide-page-element").removeClass("hidden");
                } else {
                    $(".hide-page-element").addClass("hidden");
                }
            }
        })();
    </script>
    <script>
        function handleSelectChangeLang(type) {
            var selectElement = type === "terms" ? document.getElementById("termsLocal") : document.getElementById(
                "privacyLocal");
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var lang = selectedOption.value;

            $.ajax({
                type: 'POST',
                url: "/dashboard/admin/settings/get-privacy-terms-content",
                data: {
                    type: type,
                    lang: lang
                },
                success: function(response) {
                    var content = response.content;
                    var editorId = response.type === "terms" ? "terms_content" : "privacy_content";
                    if (content !== null) {
                        tinymce.get(editorId).setContent(content);
                    } else {
                        tinymce.get(editorId).setContent("");
                    }
                },
                error: function(data) {
                    var err = data.responseJSON.errors;
                    $.each(err, function(index, value) {
                        toastr.error(value);
                    });
                }
            });
        }
    </script>
@endpush
