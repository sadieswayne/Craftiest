@extends('panel.layout.settings')
@section('title', __('Frontend Settings'))
@section('titlebar_actions', '')

@section('settings')
    <form
        id="settings_form"
        onsubmit="return frontendSettingsSave();"
        enctype="multipart/form-data"
    >
        <div
            class="alert alert-info alert-deprecated"
            role="alert"
        >
            <svg
                class="inline align-middle"
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
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
                <path
                    d="M9.103 2h5.794a3 3 0 0 1 2.122 .879l4.101 4.1a3 3 0 0 1 .88 2.125v5.794a3 3 0 0 1 -.879 2.122l-4.1 4.101a3 3 0 0 1 -2.123 .88h-5.795a3 3 0 0 1 -2.122 -.88l-4.101 -4.1a3 3 0 0 1 -.88 -2.124v-5.794a3 3 0 0 1 .879 -2.122l4.1 -4.101a3 3 0 0 1 2.125 -.88z"
                >
                </path>
                <path d="M12 9h.01"></path>
                <path d="M11 12h1v4h1"></path>
            </svg>
            <span
                class="cursor-pointer"
                onclick="return showDeprecated();"
            >
                {{ __('Some of the inputs were deprecated. Show them and edit.') }}
            </span>
        </div>
        <h3 class="mb-[25px] text-[20px]">{{ __('General Settings') }}</h3>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site Name') }}</label>
                    <input
                        class="form-control"
                        id="site_name"
                        type="text"
                        name="site_name"
                        value="{{ $setting->site_name }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site URL') }}</label>
                    <input
                        class="form-control"
                        id="site_url"
                        type="text"
                        name="site_url"
                        value="{{ $setting->site_url }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site Email') }}</label>
                    <input
                        class="form-control"
                        id="site_email"
                        type="text"
                        name="site_email"
                        value="{{ $setting->site_email }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Registration Active') }}</label>
                    <select
                        class="form-select"
                        id="register_active"
                        name="register_active"
                    >
                        <option
                            value="1"
                            {{ $setting->register_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $setting->register_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

			<div class="col-md-12">
				<div class="mb-3">
					<label class="form-label">{{ __('Registration Active') }}</label>
					<select
						class="form-select"
						id="register_active"
						name="register_active"
					>
						<option
							value="1"
							{{ $setting->register_active == 1 ? 'selected' : '' }}
						>
							{{ __('Active') }}</option>
						<option
							value="0"
							{{ $setting->register_active == 0 ? 'selected' : '' }}
						>
							{{ __('Passive') }}</option>
					</select>
				</div>
			</div>

			<div class="col-md-12">
				<div class="mb-3">
					<label class="form-label">{{ __('Facebook domain verification') }}</label>
					<input
						class="form-control"
						id="facebook_domain_verification"
						type="text"
						name="facebook_domain_verification"
						value="{{ setting('facebook_domain_verification', '') }}"
					>
				</div>
			</div>

			<div class="col-md-12">
				<div class="mb-3">
					<label class="form-label">{{ __('Google No Index') }}</label>
					<select
						class="form-select"
						id="google_robots"
						name="google_robots"
					>
						<option
							value="1"
							{{ setting('google_robots', '0') == 1 ? 'selected' : '' }}
						>
							{{ __('Active') }}</option>
						<option
							value="0"
							{{ setting('google_robots', '0') == 0 ? 'selected' : '' }}
						>
							{{ __('Passive') }}</option>
					</select>
				</div>
			</div>
        </div>

        <div class="row mb-4">
            <h3 class="mb-[25px] text-[20px]">{{ __('Frontend Settings') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('PreHeader Section') }}</label>
                    <select
                        class="form-select"
                        id="preheader_active"
                        name="preheader_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->preheader_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->preheader_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12 deprecated hidden">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('PreHeader Title') }}
                        <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                    </label>
                    <input
                        class="form-control"
                        id="header_title"
                        type="text"
                        name="header_title"
                        value="{{ $fSetting->header_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12 deprecated hidden">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('PreHeader Text') }}
                        <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                    </label>
                    <input
                        class="form-control"
                        id="header_text"
                        type="text"
                        name="header_text"
                        value="{{ $fSetting->header_text }}"
                    >
                </div>
            </div>

            <div class="col-md-12 deprecated hidden">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('Sign In Text') }}
                        <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                    </label>
                    <input
                        class="form-control"
                        id="sign_in"
                        type="text"
                        name="sign_in"
                        value="{{ $fSetting->sign_in }}"
                    >
                </div>
            </div>

            <div class="col-md-12 deprecated hidden">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('Sign Up Text') }}
                        <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                    </label>
                    <input
                        class="form-control"
                        id="join_hub"
                        type="text"
                        name="join_hub"
                        value="{{ $fSetting->join_hub }}"
                    >
                </div>
            </div>

            <div class="col-md-12 deprecated hidden">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('Hero Subtitle') }}
                        <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                    </label>
                    <input
                        class="form-control"
                        id="hero_subtitle"
                        type="text"
                        name="hero_subtitle"
                        value="{{ $fSetting->hero_subtitle }}"
                    >
                </div>
            </div>

            <div class="col-md-12 deprecated hidden">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('Hero Title') }}
                        <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                    </label>
                    <input
                        class="form-control"
                        id="hero_title"
                        type="text"
                        name="hero_title"
                        value="{{ $fSetting->hero_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Hero Title Text Rotator') }}</label>
                    <input
                        class="form-control"
                        id="hero_title_text_rotator"
                        type="text"
                        name="hero_title_text_rotator"
                        value="{{ $fSetting->hero_title_text_rotator }}"
                    >
                    <x-alert class="mt-2">
                        <p>
                            {{ __('Please use comma seperated like; Generator,Chatbot,Assistant') }}
                        </p>
                    </x-alert>
                </div>

            </div>

            <div class="col-md-12 deprecated hidden">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('Hero Description') }}
                        <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                    </label>
                    <input
                        class="form-control"
                        id="hero_description"
                        type="text"
                        name="hero_description"
                        value="{{ $fSetting->hero_description }}"
                    >
                </div>
            </div>
            <div class="col-md-12 deprecated hidden">
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('Hero Scroll Text') }}
                        <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                    </label>
                    <input
                        class="form-control"
                        id="hero_scroll_text"
                        type="text"
                        name="hero_scroll_text"
                        value="{{ $fSetting->hero_scroll_text }}"
                    >
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Hero Button') }}</label>
                    <input
                        class="form-control"
                        id="hero_button"
                        type="text"
                        name="hero_button"
                        value="{{ $fSetting->hero_button }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Hero Button Type') }}<x-info-tooltip text="{{ __('This will affect the button style') }}" /></label>

                    <select
                        class="form-select"
                        id="hero_button_type"
                        name="hero_button_type"
                    >
                        <option
                            value="1"
                            {{ $fSetting->hero_button_type == 1 ? 'selected' : '' }}
                        >
                            {{ __('Website') }}</option>
                        <option
                            value="0"
                            {{ $fSetting->hero_button_type == 0 ? 'selected' : '' }}
                        >
                            {{ __('Video') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Hero Button URL') }}</label>
                    <input
                        class="form-control"
                        id="hero_button_url"
                        type="text"
                        name="hero_button_url"
                        value="{{ $fSetting->hero_button_url }}"
                    >
                </div>
            </div>





            <div class="col-md-12">
                <div class="mb-3">
                    <div class="group relative w-full">
                        <button
                            class="mb-3 flex w-full items-center gap-5 text-sm text-heading-foreground"
                            type="button"
                        >
                            <span
                                class="size-12 inline-grid place-items-center rounded-full bg-foreground/[7%] text-heading-foreground transition-colors group-hover:bg-heading-foreground group-hover:text-heading-background"
                            >
                                <x-tabler-plus />
                            </span>
                            @lang('Hero Image')
                        </button>
                        <x-forms.input
                            class="absolute inset-0 z-2 h-full w-full cursor-pointer opacity-0"
                            class:label="leading-tight text-foreground/30"
                            id="hero_image"
                            container-class="static max-w-[270px] mx-auto"
                            size="lg"
                            name="hero_image"
                            type="file"
                            placeholder="{{ __('Upload Hero Image') }}"
                        />
                    </div>
                    @if ($fSetting->hero_image)
                        <div class="my-2">
                            <img
                                class="w-full rounded-lg object-cover"
                                src="{{ $fSetting->hero_image }}"
                                alt="hero image"
                            >
                        </div>
                    @endif

					@if(setting('front_theme') === 'social-media')
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">{{ __('No credit cart required') }}</label>
								<input
									class="form-control"
									id="no_credit_cart_required"
									type="text"
									name="no_credit_cart_required"
									value="{{ $fSetting->no_credit_cart_required }}"
								>
							</div>
						</div>

						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">
									{{ __('Faster content creation') }}
								</label>
								<textarea
									class="form-control"
									id="faster_content_creation"
									name="faster_content_creation"
								>{!! $fSetting->faster_content_creation !!}</textarea>
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">
									{{ __('Over 5000 businesses') }}
								</label>
								<textarea
									class="form-control"
									id="over_5000_businesses"
									name="over_5000_businesses"
								>{!! $fSetting->over_5000_businesses !!}</textarea>
							</div>
						</div>
					@endif


					<div class="col-md-12 deprecated hidden">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Footer Header') }}
                                <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                            </label>
                            <input
                                class="form-control"
                                id="footer_header"
                                type="text"
                                name="footer_header"
                                value="{{ $fSetting->footer_header }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12 deprecated hidden">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Footer Header Small Text') }}
                                <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                            </label>
                            <input
                                class="form-control"
                                id="footer_text_small"
                                type="text"
                                name="footer_text_small"
                                value="{{ $fSetting->footer_text_small }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-12 deprecated hidden">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Footer Text') }}
                                <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                            </label>
                            <input
                                class="form-control"
                                id="footer_text"
                                type="text"
                                name="footer_text"
                                value="{{ $fSetting->footer_text }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12 deprecated hidden">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Footer Button Text') }}
                                <span class="text-red-500/80">{{ __('Deprecated') }}</span>
                            </label>
                            <input
                                class="form-control"
                                id="footer_button_text"
                                type="text"
                                name="footer_button_text"
                                value="{{ $fSetting->footer_button_text }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Footer Button URL (Please enter full url)') }}</label>
                            <input
                                class="form-control"
                                id="footer_button_url"
                                type="text"
                                name="footer_button_url"
                                value="{{ $fSetting->footer_button_url }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Footer Copyright') }}
                            </label>
                            <input
                                class="form-control"
                                id="footer_copyright"
                                type="text"
                                name="footer_copyright"
                                value="{{ $fSetting->footer_copyright }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <x-forms.input
                                id="footer_text_color"
                                name="footer_text_color"
                                label="{{ __('Footer Text Color') }}"
                                size="lg"
                                type="color"
                                value="{{ $fSetting->footer_text_color }}"
                                tooltip="{{ __('Pick a color for for the icon container shape. Color is in HEX format.') }}"
                            />
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Pricing Section') }}
                            </label>
                            <select
                                class="form-select"
                                id="frontend_pricing_section"
                                name="frontend_pricing_section"
                            >
                                <option
                                    value="1"
                                    {{ $setting->frontend_pricing_section == 1 ? 'selected' : '' }}
                                >
                                    {{ __('Active') }}</option>
                                <option
                                    value="0"
                                    {{ $setting->frontend_pricing_section == 0 ? 'selected' : '' }}
                                >
                                    {{ __('Passive') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Custom Templates Section') }}</label>
                            <select
                                class="form-select"
                                id="frontend_custom_templates_section"
                                name="frontend_custom_templates_section"
                            >
                                <option
                                    value="1"
                                    {{ $setting->frontend_custom_templates_section == 1 ? 'selected' : '' }}
                                >
                                    {{ __('Active') }}</option>
                                <option
                                    value="0"
                                    {{ $setting->frontend_custom_templates_section == 0 ? 'selected' : '' }}
                                >
                                    {{ __('Passive') }}</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="row mb-4">
                    <h3 class="mb-[25px] text-[20px]">{{ __('Floating Button') }}</h3>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <select
                                class="form-select"
                                id="floating_button_active"
                                name="floating_button_active"
                            >
                                <option
                                    value="1"
                                    {{ $fSetting->floating_button_active == 1 ? 'selected' : '' }}
                                >
                                    {{ __('Active') }}</option>
                                <option
                                    value="0"
                                    {{ $fSetting->floating_button_active == 0 ? 'selected' : '' }}
                                >
                                    {{ __('Passive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 floating-button-input">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Floating Button Small Text') }}</label>
                            <input
                                class="form-control"
                                id="floating_button_small_text"
                                type="text"
                                name="floating_button_small_text"
                                value="{{ $fSetting->floating_button_small_text }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-12 floating-button-input">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Floating Button Bold Text') }}</label>
                            <input
                                class="form-control"
                                id="floating_button_bold_text"
                                type="text"
                                name="floating_button_bold_text"
                                value="{{ $fSetting->floating_button_bold_text }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-12 floating-button-input">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Floating Button URL') }}</label>
                            <input
                                class="form-control"
                                id="floating_button_link"
                                type="text"
                                name="floating_button_link"
                                value="{{ $fSetting->floating_button_link }}"
                            >
                        </div>
                    </div>

                    <div class="floating-button-input mt-2 flex items-center justify-center">
                        <a
                            class="flex max-w-max items-center gap-3 rounded-xl bg-white px-3 py-2 text-sm text-[#002A40] text-opacity-60 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:scale-110 hover:no-underline hover:shadow-md"
                            id="floating_button_preview"
                            data-fslightbox="html5-youtube-videos"
                            href="{{ !empty($fSetting->floating_button_link) ? $fSetting->floating_button_link : '#' }}"
                        >
                            <span
                                class="lqd-is-in-view inline-flex shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#3655df] via-[#A068FA] via-70% to-[#327BD1]"
                            >
                                <svg
                                    style="padding: 16px;"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="45"
                                    height="45"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
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
                                    <path
                                        d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z"
                                        stroke-width="0"
                                        fill="#fff"
                                    ></path>
                                </svg>
                            </span>
                            <p class="[&amp;_strong]:block pt-2">{!! __($fSetting->floating_button_small_text ?? 'See it in action') !!}<strong class="text-[0.9rem] text-black">{!! __($fSetting->floating_button_bold_text ?? 'How it Works?') !!} &nbsp;</strong></p>
                        </a>
                    </div>
                </div>

                <div class="row mb-4">
                    <h3 class="mb-[25px] text-[20px]">{{ __('Footer Social Media Settings') }}</h3>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Facebook {{ __('Address') }}</label>
                            <input
                                class="form-control"
                                id="frontend_footer_facebook"
                                type="text"
                                name="frontend_footer_facebook"
                                value="{{ $setting->frontend_footer_facebook }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Twitter {{ __('Address') }}</label>
                            <input
                                class="form-control"
                                id="frontend_footer_twitter"
                                type="text"
                                name="frontend_footer_twitter"
                                value="{{ $setting->frontend_footer_twitter }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Instagram {{ __('Address') }}</label>
                            <input
                                class="form-control"
                                id="frontend_footer_instagram"
                                type="text"
                                name="frontend_footer_instagram"
                                value="{{ $setting->frontend_footer_instagram }}"
                            >
                        </div>
                    </div>

                </div>

                <div class="row mb-4">
                    <h3 class="mb-[25px] text-[20px]">{{ __('Advanced Settings') }}</h3>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Custom Landing Page URL') }}</label>
                            <input
                                class="form-control"
                                id="frontend_additional_url"
                                type="text"
                                name="frontend_additional_url"
                                value="{{ $setting->frontend_additional_url }}"
                            >
                            <x-alert class="!mt-2">
                                <p>
                                    {{ __('Please provide full URL with http:// or https://') }}
                                </p>
                            </x-alert>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Custom CSS URL') }}</label>
                            <input
                                class="form-control"
                                id="frontend_custom_css"
                                type="text"
                                name="frontend_custom_css"
                                value="{{ $setting->frontend_custom_css }}"
                            >
                            <x-alert class="!mt-2">
                                <p>
                                    {{ __('Please provide full URL with http:// or https://') }}
                                </p>
                            </x-alert>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Custom JS URL') }}</label>
                            <input
                                class="form-control"
                                id="frontend_custom_js"
                                type="text"
                                name="frontend_custom_js"
                                value="{{ $setting->frontend_custom_js }}"
                            >
                            <x-alert class="!mt-2">
                                <p>
                                    {{ __('Please provide full URL with http:// or https://') }}
                                </p>
                            </x-alert>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Code before </head>') }}
                                <x-info-tooltip text="{{ __('Only accepts javascript code wrapped with <script> tags and HTML markup that is valid inside the </head> tag.') }}" />
                            </label>
                            <textarea
                                class="form-control"
                                id="frontend_code_before_head"
                                name="frontend_code_before_head"
                            >{{ $setting->frontend_code_before_head }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Code before </body>') }}
                                <x-info-tooltip text="{{ __('Only accepts javascript code wrapped with <script> tags and HTML markup that is valid inside the </body> tag.') }}" />
                            </label>
                            <textarea
                                class="form-control"
                                id="frontend_code_before_body"
                                name="frontend_code_before_body"
                            >{{ $setting->frontend_code_before_body }}</textarea>
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
			</div>
		</div>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script
        src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"
        type="text/javascript"
        charset="utf-8"
    ></script>
    <style
        type="text/css"
        media="screen"
    >
        .ace_editor {
            min-height: 200px;
        }
    </style>
    <script>
        var frontend_code_before_head = ace.edit("frontend_code_before_head");
        frontend_code_before_head.session.setMode("ace/mode/html");

        var frontend_code_before_body = ace.edit("frontend_code_before_body");
        frontend_code_before_body.session.setMode("ace/mode/html");

		if($('#faster_content_creation')) {
			var faster_content_creation = ace.edit("faster_content_creation");
			frontend_code_before_body.session.setMode("ace/mode/html");
		}

		if($('#over_5000_businesses')){
			var over_5000_businesses = ace.edit("over_5000_businesses");
			over_5000_businesses.session.setMode("ace/mode/html");
		}

	</script>
    <script>
        function showDeprecated() {
            $('.deprecated').toggleClass('hidden');
            toastr.success(@json(__('Deprecated inputs are editable now.')))
        }
    </script>
    <script>
        $(document).ready(function() {
            'use strict';

            if ($('#floating_button_active').val() == '0') {
                $('.floating-button-input').hide();
            }

            $('#floating_button_active').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue == '1') {
                    $('.floating-button-input').show();
                } else {
                    $('.floating-button-input').hide();
                }
            });

            var smallTextInput = $('#floating_button_small_text');
            var boldTextInput = $('#floating_button_bold_text');
            var previewLink = $('#floating_button_preview');

            smallTextInput.on('input', function() {
                var smallText = smallTextInput.val() || "See it in action";
                var boldText = boldTextInput.val() || "How it Works?";
                updatePreview(smallText, boldText);
            });

            boldTextInput.on('input', function() {
                var smallText = smallTextInput.val() || "See it in action";
                var boldText = boldTextInput.val() || "How it Works?";
                updatePreview(smallText, boldText);
            });

            // Function to update the preview <a> tag
            function updatePreview(smallText, boldText) {
                var updatedContent = smallText + '<strong class="text-black text-[0.9rem]">' + boldText +
                    '</strong> &nbsp;';
                previewLink.find('p').html(updatedContent).addClass('pt-4');
            }

        });
    </script>
@endpush
