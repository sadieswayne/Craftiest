@extends(config('elseyyid-location.layout'))

@php
    $fields = \DB::getSchemaBuilder()->getColumnListing('strings');
    if (!in_array('edit', $fields)) {
        \Illuminate\Support\Facades\Schema::table('strings', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->text('edit')->nullable();
        });
    }
@endphp

@section(config('elseyyid-location.content_section'))
    @include('langs::includes.tools')
    @php $codes = explode(',', $settings_two->languages); @endphp
    <h2 class="mb-6 mt-6 text-xl">{{ __('Available Languages') }}</h2>

    <div class="row">
        <div class="lang-switcher card card-body mb-3 flex flex-row items-center justify-between px-3 py-2">
            <h4 class="mb-0 flex items-center space-x-2 text-lg"><span class="text-2xl">{{ country2flag('us') }}</span> {{ ucfirst('English') }}<span class="text-xs opacity-20">en</span>
            </h4>
            <div class="flex space-x-1">
                <label class="form-check form-switch m-0">
                    <input
                        class="form-check-input"
                        id="en"
                        type="checkbox"
                        @if (in_array('en', $codes)) {{ 'checked' }} @endif
                        @if (LaravelLocalization::getCurrentLocale() === 'en') {{ 'disabled' }} @endif
                    >
                </label>
                <a
                    class="btn border-none shadow-none"
                    href="{{ route('elseyyid.translations.lang', 'edit') }}"
                >{{ __('Edit default strings') }}</a>
            </div>
        </div>
        <hr class="mb-4 mt-3">
        <style>
            .dropdown-toggle::after {
                display: none
            }
        </style>
        @foreach ($langs as $lang)
            @if ($lang == 'pt_BR' || $lang == 'edit')
                @continue
            @endif
            @php $lang_region = LaravelLocalization::getSupportedLocales()[str_replace('_','-',$lang)]['regional']; @endphp
            @php $lang_native = LaravelLocalization::getSupportedLocales()[str_replace('_','-',$lang)]['native']; @endphp
            <div class="lang-switcher card card-body mb-3 flex flex-row items-center justify-between px-3 py-2">
                <h4 class="mb-0 flex items-center space-x-2 text-lg"><span class="text-2xl">{{ country2flag(substr($lang_region, strrpos($lang_region, '_') + 1)) }}</span>
                    {{ ucfirst($lang_native) }}<span class="text-xs opacity-20">{{ $lang }}</span></h4>
                <div class="flex space-x-1">
                    <label class="form-check form-switch m-0">
                        <input
                            class="form-check-input"
                            id="{{ $lang }}"
                            type="checkbox"
                            @if (in_array($lang, $codes)) {{ 'checked' }} @endif
                            @if (LaravelLocalization::getCurrentLocale() === $lang) {{ 'disabled' }} @endif
                        >
                    </label>
                    <div class="dropdown">
                        <a
                            class="btn dropdown-toggle !border-none !shadow-none"
                            data-bs-toggle="dropdown"
                            href="#"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="18"
                                height="18"
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
                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                <path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                <path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                            </svg>
                        </a>
                        <div class="dropdown-menu">
                            <a
                                class="dropdown-item border-l-0 border-r-0 border-t-0 border-solid border-[--tblr-border-color] px-3 py-2 text-heading-foreground transition-colors last:border-b-0 hover:bg-[--tblr-border-color] hover:no-underline"
                                href="{{ route('elseyyid.translations.lang', $lang) }}"
                            >
                                <svg
                                    class="mr-2"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
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
                                    <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4"></path>
                                    <path d="M13.5 6.5l4 4"></path>
                                </svg>
                                {{ __('Edit Strings') }}
                            </a>
                            <a
                                class="dropdown-item text-heading border-l-0 border-r-0 border-t-0 border-solid border-[--tblr-border-color] px-3 py-2 transition-colors last:border-b-0 hover:bg-[--tblr-border-color] hover:no-underline"
                                @if ($app_is_demo) href="javascript:void(0);" onclick="return toastr.info('This feature is disabled in Demo version.')" @else href="{{ route('elseyyid.translations.lang.generateJson', $lang) }}" @endif
                            >
                                <svg
                                    class="mr-2"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
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
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M14 4l0 4l-6 0l0 -4"></path>
                                </svg>
                                <div>{{ __('Generate JSON File') }}</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
