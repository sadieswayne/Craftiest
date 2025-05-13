@php
    $steps = ['Topic', 'Title', 'Outline', 'Image'];

    $creativity_levels = [
        '0.25' => 'Economic',
        '0.5' => 'Average',
        '0.75' => 'Good',
        '1' => 'Premium',
    ];
@endphp

<div
    class="lqd-article-wizard group/article-wizard grid grid-flow-row group-[&.showing-results]/article-wizard:[grid-template-columns:100%] lg:grid-flow-col lg:[grid-template-columns:41%_59%] [&.showing-results]:[grid-template-columns:100%]"
    data-step="0"
    style="--current-step: 0;"
    x-data="{ advancedSettingsShowing: false, 'activeFilter': 'Keywords' }"
>
    <div
        class="lqd-article-wizard-prompt-area w-full group-[&.showing-results]/article-wizard:hidden"
        id="settings"
    >
        <div class="flex w-full flex-col gap-6 lg:pe-14">
            <x-card
                class="lqd-steps lqd-article-wizard-steps mb-2"
                class:body="flex flex-col gap-3"
                size="lg"
                variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
                size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : 'lg' }}"
                roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
            >
                <div class="flex flex-wrap justify-between">
                    @foreach ($steps as $step)
                        <button
                            data-step="{{ $loop->index }}"
                            @class([
                                'step group/step flex w-1/4 cursor-pointer flex-wrap items-center justify-center gap-2 rounded-md p-2 text-2xs leading-tight font-medium text-heading-foreground transition-colors hover:bg-foreground/5',
                                'active' => $loop->index === 0,
                            ])
                        >
                            <span
                                class="m-0 flex size-6 items-center justify-center rounded-lg text-center transition-colors group-[&.active]/step:bg-primary group-[&.active]/step:text-primary-foreground"
                            >
                                {{ $loop->index + 1 }}
                            </span>
                            <span class="hidden group-[&.active]/step:block">
                                {{ __($step) }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <div class="lqd-progress relative h-2 w-full overflow-hidden rounded-full bg-foreground/5">
                    <div class="lqd-progress-bar absolute h-2 w-[calc((var(--current-step)+1)/4*100%)] rounded-full bg-gradient-to-br from-[#82E2F4] to-[#8A8AED]"></div>
                </div>
            </x-card>

            <x-card
                class:body="flex w-full flex-col gap-6"
                variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
                size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md') }}"
                roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
            >
                <form
                    class="flex flex-col gap-5"
                    id="article_wizard_setting_form"
                >
                    @includeIf('panel.user.article_wizard.components.serper_seo_aw_keyword')

                    <x-forms.input
                        id="txtforkeyword"
                        size="lg"
                        container-class="w-full topic group-[:not([data-step='0'])]/article-wizard:hidden"
                        label="{{ __('Topic') }}"
                        type="textarea"
                        placeholder="{{ __('What is this article about?') }}"
                        name="txtforkeyword"
                        rows="5"
                    />

                    <x-forms.input
                        id="txtfortitle"
                        size="lg"
                        container-class="w-full topic group-[:not([data-step='1'])]/article-wizard:hidden"
                        label="{{ __('Title Topic(Optional)') }}"
                        type="textarea"
                        placeholder="{{ __('Explain your idea') }}"
                        name="txtfortitle"
                        rows="5"
                    />

                    <x-forms.input
                        id="txtforoutline"
                        size="lg"
                        container-class="w-full topic group-[:not([data-step='2'])]/article-wizard:hidden"
                        label="{{ __('Outline Topic(Optional)') }}"
                        type="textarea"
                        placeholder="{{ __('Explain your idea') }}"
                        name="txtforoutline"
                        rows="5"
                    />

                    <x-forms.input
                        id="txtforimage"
                        size="lg"
                        container-class="w-full topic group-[:not([data-step='3'])]/article-wizard:hidden"
                        label="{{ __('Explain Your Image(Optional)') }}"
                        type="textarea"
                        placeholder="{{ __('Riding horse on mars') }}"
                        name="txtforimage"
                        rows="5"
                    />

                    <x-forms.input
                        id="number_of_keywords"
                        size="lg"
                        container-class="w-full setting group-[:not([data-step='0'])]/article-wizard:hidden"
                        label="{{ __('Number of Keywords') }}"
                        type="number"
                        placeholder="{{ __('Number of keywords') }}"
                        name="number_of_keywords"
                        value="10"
                        min="5"
                        max="50"
                    />

                    <div class="setting flex flex-col gap-5 group-[:not([data-step='1'])]/article-wizard:hidden">

                        <x-forms.input
                            id="keywords"
                            size="lg"
                            container-class="w-full"
                            label="{{ __('Number of Keywords') }}"
                            placeholder="{{ __('Keywords') }}"
                            name="keywords"
                            readonly
                        />

                        <x-forms.input
                            id="number_of_titles"
                            size="lg"
                            container-class="w-full"
                            label="{{ __('Number of Titles') }}"
                            type="number"
                            placeholder="{{ __('Number of titles') }}"
                            name="number_of_titles"
                            value="3"
                            min="3"
                            max="15"
                        />

                        <x-forms.input
                            id="title_length"
                            size="lg"
                            container-class="w-full"
                            label="{{ __('Maximum Title length') }}"
                            type="number"
                            placeholder="{{ __('Maximum Title length') }}"
                            name="title_length"
                            value="30"
                            min="20"
                            max="100"
                        />
                    </div>

                    <div class="setting flex flex-col gap-5 group-[:not([data-step='2'])]/article-wizard:hidden">
                        <x-forms.input
                            id="keywords_outline"
                            size="lg"
                            container-class="w-full"
                            label="{{ __('Keywords') }}"
                            placeholder="{{ __('Keywords') }}"
                            name="keywords_outline"
                            readonly
                        />

                        <x-forms.input
                            id="number_of_outline_subtitles"
                            size="lg"
                            container-class="w-full"
                            label="{{ __('Number of Subtitles') }}"
                            type="number"
                            placeholder="{{ __('Number of Subtitles') }}"
                            name="number_of_outline_subtitles"
                            value="10"
                            min="5"
                            max="20"
                        />

                        <x-forms.input
                            id="number_of_outlines"
                            size="lg"
                            container-class="w-full"
                            label="{{ __('Number of Outlines') }}"
                            type="number"
                            placeholder="{{ __('Number of Outlines') }}"
                            name="number_of_outlines"
                            value="3"
                            min="3"
                            max="5"
                        />
                    </div>

                    <div class="setting flex flex-col gap-5 group-[:not([data-step='3'])]/article-wizard:hidden">
                        <x-forms.input
                            id="size_of_images"
                            size="lg"
                            container-class="w-full"
                            label="{{ __('Size (Optional)') }}"
                            type="select"
                            name="size_of_images"
                        >
                            <option value="thumb">Very Small</option>
                            <option value="small">Small</option>
                            <option value="small_s3">Normal</option>
                            <option value="full">Big</option>
                            <option value="raw">Very Big</option>
                        </x-forms.input>

                        <x-forms.input
                            id="number_of_images"
                            size="lg"
                            container-class="w-full"
                            label="{{ __('Number of images') }}"
                            type="number"
                            placeholder="{{ __('Number of images') }}"
                            name="number_of_images"
                            value="4"
                            min="1"
                            max="6"
                        />
                    </div>

                    <x-button
                        class="w-full gap-9"
                        variant="link"
                        x-data="{}"
                        @click.prevent="advancedSettingsShowing = !advancedSettingsShowing"
                    >
                        <span class="h-px grow bg-foreground/10"></span>
                        <span class="flex items-center gap-2">
                            {{ __('Advanced Options') }}
                            <x-tabler-chevron-down class="size-4" />
                        </span>
                        <span class="h-px grow bg-foreground/10"></span>
                    </x-button>

                    <div
                        class="hidden flex-col gap-6"
                        :class="{ 'hidden': !advancedSettingsShowing, 'flex': advancedSettingsShowing }"
                    >
                        <div class="result_count hidden empty:hidden"></div>
                        <div class="result_count hidden empty:hidden"></div>
                        <div class="result_count hidden empty:hidden"></div>
                        <div class="result_count hidden empty:hidden"></div>

                        <x-forms.input
                            id="language"
                            name="language"
                            type="select"
                            size="lg"
                            label="{{ __('Language') }}"
                        >
                            @include('panel.user.openai.components.countries')
                        </x-forms.input>

                        <x-forms.input
                            id="blog_post_length"
                            name="blog_post_length"
                            label="{{ __('Blog Post Length') }}"
                            placeholder="800"
                            size="lg"
                        />

                        @if (setting('hide_creativity_option') != 1)
                            <x-forms.input
                                id="creativity"
                                name="creativity"
                                type="select"
                                label="{{ __('Creativity') }}"
                                size="lg"
                                required
                            >
                                @foreach ($creativity_levels as $creativity => $label)
                                    <option
                                        value="{{ $creativity }}"
                                        @selected($setting->openai_default_creativity == $creativity)
                                    >
                                        {{ __($label) }}
                                    </option>
                                @endforeach
                            </x-forms.input>
                        @endif
                    </div>

                    <x-button
                        id="generator_btn"
                        tag="button"
                        size="lg"
                        type="submit"
                    >
                        <span class="hidden group-[.lqd-form-submitting]:inline-flex">
                            {{ __('Please wait...') }}
                        </span>
                        <span class="generate_title hidden group-[.lqd-form-submitting]:hidden">
                            {{ __('Generate Keywords') }}
                        </span>
                        <span class="generate_title hidden group-[.lqd-form-submitting]:hidden">
                            {{ __('Generate Title') }}
                        </span>
                        <span class="generate_title hidden group-[.lqd-form-submitting]:hidden">
                            {{ __('Generate Outline') }}
                        </span>
                        <span class="generate_title hidden group-[.lqd-form-submitting]:hidden">
                            {{ __('Generate Image') }}
                        </span>
                    </x-button>

                    <x-button
                        class="hidden"
                        id="skip_image"
                        variant="secondary"
                        tag="button"
                    >
                        {{ __('Skip this step') }}
                    </x-button>

                </form>
            </x-card>

            {{-- search questions --}}
            <x-card
                class="hidden group-[:not([data-step='2'])]/article-wizard:hidden"
                id="search_questions_card"
            >
                <h1 class="form-label mb-4">{{ __('Search Questions:') }}</h1>

                <textarea
                    class="w-full"
                    id="search_questions"
                    label="{{ __('Search Questions') }}"
                    @readonly(true)
                    placeholder="{{ __('Search Questions') }}"
                    name="search_questions"
                    rows="5"
                ></textarea>
            </x-card>

        </div>
    </div>

    <x-card
        @class([
            'lqd-article-wizard-meta-area group/meta-area ms-auto w-full group-[&.showing-results]/article-wizard:mx-auto group-[&.showing-results]/article-wizard:border-none w-full',
            ' group-[&.showing-results]/article-wizard:ps-0 sm:border-s sm:ps-10 md:ps-20' =>
                Theme::getSetting('defaultVariations.card.variant', 'outline') ===
                'outline',
        ])
        variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
        size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md') }}"
        roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
    >
        <div
            class="hidden w-full flex-col items-center justify-center gap-2 text-center [&.active]:flex"
            id="final_settings"
        >
            {{-- blade-formatter-disable --}}
            <svg class="mb-5 mx-auto" width="27" height="25" viewBox="0 0 27 25" fill="none" xmlns="http://www.w3.org/2000/svg" >
                <path d="M25.3004 9.67896L23.1777 10.1093C22.1551 10.3169 21.2163 10.821 20.4785 11.5589C19.7406 12.2967 19.2365 13.2355 19.0289 14.2581L18.5986 16.3808C18.556 16.5931 18.4412 16.7841 18.2737 16.9213C18.1063 17.0585 17.8965 17.1335 17.68 17.1335C17.4635 17.1335 17.2536 17.0585 17.0862 16.9213C16.9187 16.7841 16.8039 16.5931 16.7613 16.3808L16.3309 14.2581C16.1236 13.2355 15.6195 12.2965 14.8816 11.5586C14.1438 10.8208 13.2049 10.3167 12.1822 10.1093L10.0595 9.67896C9.84761 9.63563 9.65721 9.52048 9.52049 9.35296C9.38376 9.18544 9.30908 8.97583 9.30908 8.7596C9.30908 8.54337 9.38376 8.33378 9.52049 8.16627C9.65721 7.99875 9.84761 7.8836 10.0595 7.84027L12.1822 7.40986C13.2049 7.20252 14.1438 6.69843 14.8816 5.96057C15.6195 5.2227 16.1236 4.28378 16.3309 3.26109L16.7613 1.13839C16.8039 0.92612 16.9187 0.73514 17.0862 0.597929C17.2536 0.460718 17.4635 0.385742 17.68 0.385742C17.8965 0.385742 18.1063 0.460718 18.2737 0.597929C18.4412 0.73514 18.556 0.92612 18.5986 1.13839L19.0289 3.26109C19.2365 4.2837 19.7406 5.22253 20.4785 5.96036C21.2163 6.6982 22.1551 7.20234 23.1777 7.40986L25.3004 7.84027C25.5123 7.8836 25.7026 7.99875 25.8394 8.16627C25.9761 8.33378 26.0508 8.54337 26.0508 8.7596C26.0508 8.97583 25.9761 9.18544 25.8394 9.35296C25.7026 9.52048 25.5123 9.63563 25.3004 9.67896Z" fill="url(#paint0_linear_139_6)" /> <path d="M9.77402 20.4374L9.19721 20.5545C8.48598 20.6988 7.83303 21.0494 7.31987 21.5626C6.80671 22.0757 6.4561 22.7287 6.31181 23.4399L6.19469 24.0167C6.16231 24.1782 6.07499 24.3236 5.94755 24.428C5.82011 24.5325 5.66044 24.5895 5.49568 24.5895C5.33092 24.5895 5.17124 24.5325 5.04381 24.428C4.91637 24.3236 4.82902 24.1782 4.79664 24.0167L4.67952 23.4399C4.53523 22.7287 4.18462 22.0757 3.67146 21.5626C3.1583 21.0494 2.50535 20.6988 1.79412 20.5545L1.21734 20.4374C1.0558 20.405 0.910438 20.3177 0.806011 20.1902C0.701585 20.0628 0.644531 19.9031 0.644531 19.7383C0.644531 19.5736 0.701585 19.4139 0.806011 19.2865C0.910438 19.1591 1.0558 19.0717 1.21734 19.0393L1.79412 18.9222C2.50535 18.7779 3.1583 18.4273 3.67146 17.9141C4.18462 17.401 4.53523 16.7481 4.67952 16.0368L4.79664 15.46C4.82902 15.2985 4.91637 15.1531 5.04381 15.0487C5.17124 14.9443 5.33092 14.8872 5.49568 14.8872C5.66044 14.8872 5.82011 14.9443 5.94755 15.0487C6.07499 15.1531 6.16231 15.2985 6.19469 15.46L6.31181 16.0368C6.4561 16.7481 6.80671 17.401 7.31987 17.9141C7.83303 18.4273 8.48598 18.7779 9.19721 18.9222L9.77402 19.0393C9.93556 19.0717 10.0809 19.1591 10.1853 19.2865C10.2897 19.4139 10.3468 19.5736 10.3468 19.7383C10.3468 19.9031 10.2897 20.0628 10.1853 20.1902C10.0809 20.3177 9.93556 20.405 9.77402 20.4374Z" fill="url(#paint1_linear_139_6)" /> <defs> <linearGradient id="paint0_linear_139_6" x1="26.0508" y1="12.4876" x2="0.396179" y2="9.531" gradientUnits="userSpaceOnUse" > <stop stop-color="#8D65E9" /> <stop offset="0.483" stop-color="#5391E4" /> <stop offset="1" stop-color="#6BCD94" /> </linearGradient> <linearGradient id="paint1_linear_139_6" x1="26.0508" y1="12.4876" x2="0.396179" y2="9.531" gradientUnits="userSpaceOnUse" > <stop stop-color="#8D65E9" /> <stop offset="0.483" stop-color="#5391E4" /> <stop offset="1" stop-color="#6BCD94" /> </linearGradient> </defs>
            </svg>
			{{-- blade-formatter-enable --}}

            <h4 class="text-center text-2xl font-bold">
                <span id="result_title">
                    {{ __('Generating the article') }}...
                </span>
                <span
                    class="hidden"
                    id="result_success_title"
                >
                    {{ __('Successfully Generated') }}
                </span>
                <span
                    class="hidden"
                    id="result_abort_title"
                >
                    {{ __('Generating is aborted.') }}
                </span>
            </h4>

            <p class="mx-auto w-4/5 text-balance text-center font-medium opacity-60">
                {{ __('You can edit your article in documents once it is generated.') }}
            </p>
        </div>

        <div
            class="relative flex flex-col gap-6 [&_.tox-edit-area__iframe]:!bg-transparent"
            id="wizard_area"
        >
            <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-6 rounded-xl border p-4">
                <h4 class="m-0">
                    <span class="flex gap-2 group-[:not([data-step='0'])]/article-wizard:hidden">
                        <span class="m-0 flex size-5 items-center justify-center rounded-md bg-primary/10 text-3xs text-primary">
                            1
                        </span>
                        {{ __('Select Keywords') }}
                    </span>
                    <span class="flex gap-2 group-[:not([data-step='1'])]/article-wizard:hidden">
                        <span class="m-0 flex size-5 items-center justify-center rounded-md bg-primary/10 text-3xs text-primary">
                            2
                        </span>
                        {{ __('Choose a Title') }}
                    </span>
                    <span class="flex gap-2 group-[:not([data-step='2'])]/article-wizard:hidden">
                        <span class="m-0 flex size-5 items-center justify-center rounded-md bg-primary/10 text-3xs text-primary">
                            3
                        </span>
                        {{ __('Outline') }}
                    </span>
                    <span class="flex gap-2 group-[:not([data-step='3'])]/article-wizard:hidden">
                        <span class="m-0 flex size-5 items-center justify-center rounded-md bg-primary/10 text-3xs text-primary">
                            4
                        </span>
                        {{ __('Image (optional)') }}
                    </span>
                </h4>
                <div>
                    <x-modal
                        class:modal-backdrop="backdrop-blur-none bg-foreground/15"
                        type="inline"
                        anchor="end"
                    >
                        <x-slot:trigger
                            class="gap-1 font-bold text-primary"
                            variant="link"
                        >
                            {{ __('Add') }}
                            <x-tabler-plus
                                class="size-3"
                                stroke-width="3"
                            />
                        </x-slot:trigger>

                        <x-slot:modal
                            x-data="{}"
                        >
                            <x-forms.input
                                id="new_keyword"
                                @keyup.enter="$refs.submitBtn.click(); modalOpen = false"
                                container-class="group-[:not([data-step='0'])]/article-wizard:hidden"
                                label="{{ __('Add Keyword') }}"
                                name="new_keyword"
                                placeholder="{{ __('New Keyword') }}"
                                size="lg"
                            />

                            <x-forms.input
                                id="new_title"
                                @keyup.enter="$refs.submitBtn.click(); modalOpen = false"
                                container-class="group-[&:not([data-step='1'])]/article-wizard:hidden"
                                label="{{ __('Add Title') }}"
                                name="new_title"
                                placeholder="{{ __('New Title') }}"
                                size="lg"
                            />

                            <x-forms.input
                                id="new_outline"
                                @keyup.enter="$refs.submitBtn.click(); modalOpen = false"
                                container-class="group-[&:not([data-step='2'])]/article-wizard:hidden"
                                label="{{ __('Add Outline') }}"
                                name="new_outline"
                                placeholder="{{ __('New Outline') }}"
                                size="lg"
                            />

                            <img
                                class="mb-3 hidden w-full group-[&:not([data-step='3'])]/article-wizard:hidden"
                                id="new_image"
                                src=""
                                alt="New image"
                            >
                            <x-forms.input
                                id="new_file"
                                id="new_file"
                                @keyup.enter="$refs.submitBtn.click(); modalOpen = false"
                                container-class="group-[&:not([data-step='3'])]/article-wizard:hidden"
                                type="file"
                                accept="image/*"
                                label="{{ __('Add Image') }}"
                                name="new_file"
                                placeholder="{{ __('New Image') }}"
                                size="lg"
                            />

                            <div class="mt-4 border-t pt-3 text-end">
                                <x-button
                                    @click.prevent="modalOpen = false"
                                    variant="outline"
                                >
                                    {{ __('Cancel') }}
                                </x-button>
                                <x-button
                                    id="btn_add_new"
                                    tag="button"
                                    x-ref="submitBtn"
                                >
                                    {{ __('Add') }}
                                </x-button>
                            </div>
                        </x-slot:modal>
                    </x-modal>
                </div>
            </div>

            <div class="select_area hidden w-full">
                <div class="bg w-full">
                    <button
                        class="cursor-pointer border-none bg-transparent px-0 py-2 font-semibold text-heading-foreground"
                        id="select_all_keyword"
                        type="button"
                    >
                        {{ __('Select All') }}
                    </button>
                    <button
                        class="cursor-pointer border-none bg-transparent px-0 py-2 font-semibold text-heading-foreground"
                        id="unselect_all_keyword"
                        type="button"
                    >
                        {{ __('Unselect All') }}
                    </button>
                </div>
                <div
                    class="flex w-full flex-wrap gap-3"
                    id="select_keywords"
                ></div>
            </div>
            <div class="select_area hidden w-full">
                <div
                    class="flex w-full flex-wrap gap-3"
                    id="select_title"
                ></div>
            </div>
            <div class="select_area hidden w-full">
                <div
                    class="flex w-full flex-wrap gap-3"
                    id="select_outline"
                ></div>
            </div>
            <div
                class="select_area hidden w-full grid-cols-2 gap-6 md:grid-cols-3 [&.active]:grid"
                id="select_image"
            ></div>

            <div
                class="mt-4 hidden w-full"
                id="next_btn"
            >
                <x-button
                    class="w-full"
                    id="next_page_btn"
                    variant="secondary"
                    onclick="goNextStep()"
                >
                    <span class="hidden group-[.lqd-form-submitting]:inline-flex">
                        {{ __('Please wait...') }}
                    </span>
                    <span class="flex items-center gap-2 group-[.lqd-form-submitting]:hidden">
                        {{ __('Next') }}
                        <span class="inline-flex size-7 items-center justify-center rounded-full bg-background text-foreground">
                            <x-tabler-chevron-right class="size-4" />
                        </span>
                    </span>
                </x-button>
            </div>
            <div
                class="mt-4 hidden w-full"
                id="generate_btn"
            >
                <x-button
                    class="w-full"
                    id="generate_article"
                    variant="secondary"
                    onclick="goNextStep()"
                >
                    <span class="hidden group-[.lqd-form-submitting]:inline-flex">
                        {{ __('Please wait...') }}
                    </span>
                    <span class="flex items-center gap-2 group-[.lqd-form-submitting]:hidden">
                        {{ __('Next') }}
                        <span class="inline-flex size-7 items-center justify-center rounded-full bg-background text-foreground">
                            <x-tabler-chevron-right class="size-4" />
                        </span>
                    </span>
                </x-button>
            </div>
        </div>

        <div
            class="mt-8 hidden"
            id="result_area"
        >
            <div class="mb-4 flex flex-wrap items-center justify-between">
                <div class="flex w-full items-center justify-between">
                    <h2 class="select_title_right my-0">
                        {{ __('Result') }}
                    </h2>
                    <a
                        class="hidden items-center gap-1.5 text-nowrap rounded-md bg-green-600/10 px-2.5 py-1 text-[12px] font-medium leading-none text-green-700 transition-all hover:scale-105 hover:shadow-lg hover:shadow-green-600/5 dark:text-green-400 [&.active]:flex"
                        id="saved_documents"
                        href="{{ route('dashboard.user.openai.documents.all') }}"
                    >
                        <x-tabler-folder-check class="size-5" />
                        {{ __('Saved to') }}
                        <span class="underline">{{ __('Documents') }}</span>
                    </a>
                    <x-button
                        id="stop_generating"
                        tag="button"
                        variant="danger"
                    >
                        {{ __('Stop') }}
                    </x-button>
                </div>
            </div>
            <div class="mt-8">
                @if (View::exists('panel.user.article_wizard.components.article_result') && setting('serper_seo_aw_anlyze', 0))
                    @include('panel.user.article_wizard.components.article_result')
                @else
                    <div class="w-full pe-5 ps-2">
                        <div id="result_article">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-card>
</div>

<template id="selected_keyword">
    <button class="keyword cursor-pointer rounded-full border border-secondary bg-secondary px-3 py-1 font-medium text-secondary-foreground"></button>
</template>
<template id="unselected_keyword">
    <button class="keyword cursor-pointer rounded-full border px-3 py-1 font-medium text-heading-foreground"></button>
</template>

<template id="selected_title">
    <p class="title flex w-full cursor-pointer items-center gap-3 rounded-2xl px-4 py-2.5 text-heading-foreground shadow-[0_3px_19px_rgba(47,58,99,0.06)] dark:bg-foreground/5">
        <span class="flex size-9 items-center justify-center rounded-full bg-secondary text-secondary-foreground">
            <x-tabler-check class="size-5" />
        </span>
        <span class="title_text"></span>
    </p>
</template>
<template id="unselected_title">
    <p class="title flex w-full cursor-pointer items-center gap-3 rounded-2xl border px-4 py-2.5 text-heading-foreground">
        <span class="flex size-9 items-center justify-center rounded-full bg-foreground/5"></span>
        <span class="title_text"></span>
    </p>
</template>

<template id="sample_outline_template">
    <li></li>
</template>

<template id="selected_outline">
    <div
        class="outline_ select_outline relative flex w-full cursor-pointer flex-col rounded-2xl px-6 py-4 text-heading-foreground shadow-[0_3px_19px_rgba(47,58,99,0.06)] dark:bg-foreground/5""
        data="0"
    >
        <ul class="my-0 list-inside list-disc pe-14 text-xs"></ul>
        <span class="absolute end-5 top-5 flex size-9 items-center justify-center rounded-full bg-secondary text-secondary-foreground">
            <x-tabler-check class="size-5" />
        </span>
    </div>
</template>
<template id="unselected_outline">
    <div
        class="outline_ relative flex w-full cursor-pointer flex-col rounded-2xl border px-6 py-4 text-heading-foreground"
        data="0"
    >
        <ul class="my-0 list-inside list-disc pe-14 text-xs"></ul>
    </div>
</template>

<template id="selected_image">
    <div class="image_ relative cursor-pointer">
        <img
            class="w-full rounded-2xl border shadow-2xl shadow-black/10"
            src=""
        >
        <span class="absolute -end-4 -top-4 flex size-9 items-center justify-center rounded-full bg-secondary text-secondary-foreground">
            <x-tabler-check class="size-5" />
        </span>
    </div>
</template>
<template id="unselected_image">
    <div class="image_ relative cursor-pointer">
        <img
            class="w-full rounded-2xl border"
            src=""
        >
    </div>
</template>
