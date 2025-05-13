@extends('panel.layout.settings')
@section('title', __('Seo Settings'))
@section('titlebar_actions', '')
@section('additional_css')

@endsection

@section('settings')
    <form action="{{route("dashboard.admin.config.seo.store")}}" method="POST">
        @csrf
        <h3 class="mb-[25px] text-[20px]">{{ __('Seo Settings') }}</h3>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="mb-4">
                    <label class="form-label">{{ __('Google Analytics Tracking ID') }} (UA-1xxxxx)
                        {{ __('or') }} (G-xxxxxx)</label>
                    <input
                            class="form-control"
                            id="google_analytics_code"
                            type="text"
                            name="google_analytics_code"
                            value="{{ $setting->google_analytics_code }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label m-0">{{ __('Meta Title') }}</label>
                        <select
                                class="form-control min-w-36 m-0 bg-[#F1EDFF] py-1"
                                id="metaTitleLocal"
                                style="width: auto;"
                                name="metaTitleLocal"
                                onchange="handleSelectChangeLang('meta_title');"
                        >
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @if (in_array($localeCode, explode(',', $settings_two->languages)))
                                    <option
                                            class="p-0"
                                            value="{{ $localeCode }}"
                                    @if ($settings_two->languages_default === $localeCode)
                                        {{ 'selected' }}
                                            @endif
                                    >
                                        <span class="!me-2 text-[21px]">{{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}</span>
                                        {{ ucfirst($properties['native']) }} @if ($settings_two->languages_default === $localeCode)
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <input
                            class="form-control {{ setting('serper_seo_site_meta', 0) == 1 ? 'input-seo' : '' }}"
                            id="meta_title"
                            type="text"
                            name="meta_title"
                            value="{{ $setting->meta_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label m-0">{{ __('Meta Description') }}</label>
                        <select
                                class="form-control min-w-36 m-0 bg-[#F1EDFF] py-1"
                                id="metaDescLocal"
                                style="width: auto;"
                                name="metaDescLocal"
                                onchange="handleSelectChangeLang('meta_desc');"
                        >
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @if (in_array($localeCode, explode(',', $settings_two->languages)))
                                    <option
                                            class="p-0"
                                            value="{{ $localeCode }}"
                                    @if ($settings_two->languages_default === $localeCode)
                                        {{ 'selected' }}
                                            @endif
                                    >
                                        <span class="!me-2 text-[21px]">{{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}</span>
                                        {{ ucfirst($properties['native']) }} @if ($settings_two->languages_default === $localeCode)
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <textarea
                            class="form-control {{ setting('serper_seo_site_meta', 0) == 1 ? 'input-seo' : '' }}"
                            id="meta_description"
                            name="meta_description"
                            rows="5"
                    >{{ $setting->meta_description }}</textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Meta Keywords') }}</label>
                    <textarea
                            class="form-control {{ setting('serper_seo_site_meta', 0) == 1 ? 'input-seo' : '' }}"
                            id="meta_keywords"
                            name="meta_keywords"
                            placeholder="{{ __('ChatGPT, AI Writer, AI Image Generator, AI Chat') }}"
                            rows="3"
                    >{{ $setting->meta_keywords }}</textarea>
                </div>
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
    </form>
@endsection

@push('script')
    <script>
        function handleSelectChangeLang(type) {
            var selectElement = type === "meta_title" ? document.getElementById("metaTitleLocal") : document.getElementById(
                "metaDescLocal");
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var lang = selectedOption.value;

            $.ajax({
                type: 'POST',
                url: "/dashboard/admin/settings/get-meta-content",
                data: {
                    type: type,
                    lang: lang
                },
                success: function(response) {
                    var content = response.content;
                    var inputId = response.type === "meta_title" ? "meta_title" : "meta_description";
                    if (content !== null) {
                        $("#" + inputId).val(content);
                    } else {
                        $("#" + inputId).val('');
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
        // on page first load
        handleSelectChangeLang('meta_title');
        handleSelectChangeLang('meta_desc');
    </script>
@endpush
