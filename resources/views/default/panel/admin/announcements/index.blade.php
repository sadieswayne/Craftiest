@php
    $filters = ['Light', 'Dark'];
@endphp

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Announcements'))

@push('css')
    <style>
        @if (setting('announcement_background_color'))
            .lqd-card.lqd-announcement-card {
                background-color: {{ setting('announcement_background_color') }};
            }
        @endif
        @if (setting('announcement_background_image'))
            .lqd-card.lqd-announcement-card {
                background-image: url({{ setting('announcement_background_image') }});
            }
        @endif
        @if (setting('announcement_background_color_dark'))
            .theme-dark .lqd-card.lqd-announcement-card {
                background-color: {{ setting('announcement_background_color_dark') }};
            }
        @endif
        @if (setting('announcement_background_image_dark'))
            .theme-dark .lqd-card.lqd-announcement-card {
                background-image: url({{ setting('announcement_background_image_dark') }});
            }
        @endif
    </style>
@endpush

@section('content')
    <div
        class="py-10"
        x-data="{ 'activeFilter': 'Light' }"
    >
        @if (setting('notification_active', 0) == 1)
            <form
                class="mx-auto flex w-full flex-col gap-5 lg:w-5/12"
                action="{{ route('dashboard.admin.announcements.store') }}"
                method="POST"
            >
                <x-form-step
                    step="1"
                    label="{{ __('Send Announcement') }}"
                />

                @csrf
                <x-forms.input
                    id="title"
                    size="lg"
                    label="{{ __('Title') }}"
                    tooltip="{{ __('The title of the announcement.') }}"
                    placeholder="{{ __('Title') }}"
                    name="title"
                    required
                />

                <x-forms.input
                    id="message"
                    label="{{ __('Message') }}"
                    tooltip="{{ __('The message of the announcement.') }}"
                    placeholder="{{ __('Message') }}"
                    type="textarea"
                    rows="3"
                    name="message"
                />

                <x-forms.input
                    id="url"
                    label="{{ __('URL') }}"
                    tooltip="{{ __('The URL of the announcement. Leave empty if you do not want to include a URL.') }}"
                    placeholder="{{ __('URL (optional)') }}"
                    name="url"
                />

                @if ($app_is_demo)
                    <x-button
                        type="button"
                        onclick="return toastr.info('This feature is disabled in Demo version.');"
                    >
                        {{ __('Save') }}
                    </x-button>
                @else
                    <x-button type="submit">
                        {{ __('Save') }}
                    </x-button>
                @endif

            </form>
        @endif

        <form
            class="mx-auto flex w-full flex-col gap-4 pt-10 lg:w-5/12"
            action="{{ route('dashboard.admin.announcements.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            <x-form-step
                step="2"
                label="{{ __('Dashboard Announcement') }}"
            />
            @csrf
            <p class="text-sm text-gray-500 dark:text-gray-400">
                @lang('This announcement will be displayed on the dashboard for all users.')
            </p>

            <x-card
                class="lqd-announcement-card relative bg-cover bg-center"
                size="lg"
            >
                <div class="flex items-center justify-between gap-4">
                    <div class="w-9/12">
                        <h3 class="mb-3">
                            @lang(setting('announcement_title', 'Welcome to MagicAI!'))
                        </h3>
                        <p class="mb-4">
                            @lang(setting('announcement_description', 'We are excited to have you here. Explore the marketplace to find the best AI models for your needs.'))
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <x-button
                                class="font-medium"
                                href="{{ setting('announcement_url', '#') }}"
                            >
                                <x-tabler-plus class="size-4" />

                                {{ setting('announcement_button_text', 'Try it Now') }}
                            </x-button>
                            <x-button
                                class="font-medium"
                                href="#"
                                variant="ghost-shadow"
                                hover-variant="danger"
                            >
                                @lang('Dismiss')
                            </x-button>
                        </div>
                    </div>
                    @if (setting('announcement_image_dark'))
                        <img
                            class="announcement-img announcement-img-dark peer hidden w-3/12 shrink-0 dark:block"
                            src="{{ setting('announcement_image_dark', '/upload/images/speaker.png') }}"
                            alt="@lang(setting('announcement_title', 'Welcome to MagicAI!'))"
                        >
                    @endif
                    <img
                        class="announcement-img announcement-img-light w-3/12 shrink-0 dark:peer-[&.announcement-img-dark]:hidden"
                        src="{{ setting('announcement_image', '/upload/images/speaker.png') }}"
                        alt="@lang(setting('announcement_title', 'Welcome to MagicAI!'))"
                    >
                </div>
            </x-card>

            <x-forms.input
                class:container="mb-2"
                id="announcement_active"
                type="checkbox"
                label="{{ __('Active') }}"
                tooltip="{{ __('Enable or disable the announcement.') }}"
                name="announcement_active"
                :checked="setting('announcement_active', 0) == 1"
                switcher
            />

            <x-forms.input
                id="announcement_title"
                size="lg"
                label="{{ __('Title') }}"
                tooltip="{{ __('The title of the announcement.') }}"
                placeholder="{{ __('Title') }}"
                value="{{ setting('announcement_title', 'Welcome to MagicAI!') }}"
                name="announcement_title"
                required
            />

            <x-forms.input
                id="announcement_description"
                label="{{ __('Description') }}"
                tooltip="{{ __('The description of the announcement.') }}"
                placeholder="{{ __('Description') }}"
                type="textarea"
                rows="3"
                name="announcement_description"
            >
                {!! setting('announcement_description', 'We are excited to have you here. Explore the marketplace to find the best AI models for your needs.') !!}
            </x-forms.input>

            <x-forms.input
                id="announcement_url"
                label="{{ __('URL') }}"
                tooltip="{{ __('The URL of the announcement. Leave empty if you do not want to include a URL.') }}"
                placeholder="{{ __('URL (optional)') }}"
                name="announcement_url"
                value="{{ setting('announcement_url', '#') }}"
            />

            <x-forms.input
                id="announcement_button_text"
                size="lg"
                label="{{ __('Button Text') }}"
                tooltip="{{ __('The text of the button.') }}"
                placeholder="{{ __('Button Text') }}"
                value="{{ setting('announcement_button_text', 'Try it Now') }}"
                name="announcement_button_text"
            />

            <div class="flex flex-col gap-6">
                <ul class="flex w-full justify-between gap-3 rounded-full bg-foreground/10 p-1 text-xs font-medium">
                    @foreach ($filters as $filter)
                        <li class="grow">
                            <button
                                type="button"
                                @class([
                                    'w-full rounded-full px-6 py-3 leading-tight transition-all hover:bg-background/80 [&.lqd-is-active]:bg-background [&.lqd-is-active]:shadow-[0_2px_12px_hsl(0_0%_0%/10%)]',
                                    'lqd-is-active' => $loop->first,
                                ])
                                @click="activeFilter = '{{ $filter }}'"
                                :class="{ 'lqd-is-active': activeFilter == '{{ $filter }}' }"
                            >
                                @lang($filter)
                            </button>
                        </li>
                    @endforeach
                </ul>
                <x-card
                    data-cat="Light"
                    size="none"
                    variant="none"
                    ::class="{ 'hidden': !$el.getAttribute('data-cat')?.includes(activeFilter) && activeFilter !== 'Light' }"
                >
                    <div class="group/filepicker relative">
                        <button
                            class="mb-3 flex w-full items-center gap-5 text-sm text-heading-foreground"
                            type="button"
                        >
                            <span
                                class="inline-grid size-12 place-items-center rounded-full bg-foreground/[7%] text-heading-foreground transition-colors group-hover/filepicker:bg-heading-foreground group-hover/filepicker:text-heading-background"
                            >
                                <x-tabler-plus />
                            </span>
                            @lang('Announcement Image')
                        </button>
                        <img
                            class="h-48 w-48 object-cover"
                            src="{{ setting('announcement_image', '/upload/images/speaker.png') }}"
                            alt=""
                        />
                        <x-forms.input
                            class="absolute inset-0 z-2 h-full w-full cursor-pointer opacity-0"
                            class:label="leading-tight text-foreground/30"
                            id="announcement_image"
                            container-class="static max-w-[270px] mx-auto"
                            size="lg"
                            name="announcement_image"
                            type="file"
                            placeholder="{{ __('Upload Announcement Image') }}"
                        />

                    </div>
                    <div class="group/filepicker relative">
                        <button
                            class="mb-3 flex w-full items-center gap-5 text-sm text-heading-foreground"
                            type="button"
                        >
                            <span
                                class="inline-grid size-12 place-items-center rounded-full bg-foreground/[7%] text-heading-foreground transition-colors group-hover/filepicker:bg-heading-foreground group-hover/filepicker:text-heading-background"
                            >
                                <x-tabler-plus />
                            </span>
                            @lang('Announcement Background Image (Optional)')
                        </button>
                        @if (setting('announcement_background_image'))
                            <img
                                class="mb-4 h-48 w-48 object-cover"
                                src="{{ setting('announcement_background_image', '') }}"
                                alt=""
                            />
                        @endif
                        <x-forms.input
                            class="absolute inset-0 z-2 h-full w-full cursor-pointer opacity-0"
                            class:label="leading-tight text-foreground/30"
                            id="announcement_background_image"
                            container-class="static max-w-[270px] mx-auto"
                            size="lg"
                            name="announcement_background_image"
                            type="file"
                            placeholder="{{ __('Upload Announcement Background Image') }}"
                        />
                    </div>
                    <x-forms.input
                        class="w-full"
                        id="announcement_background_color"
                        label="{{ __('Announcement Background Color') }}"
                        name="announcement_background_color"
                        value="{{ setting('announcement_background_color', '#ffffff') }}"
                        type="color"
                    />
                </x-card>
                <x-card
                    data-cat="Dark"
                    size="none"
                    variant="none"
                    ::class="{ 'hidden': !$el.getAttribute('data-cat')?.includes(activeFilter) && activeFilter !== 'Dark' }"
                >

                    <div class="group/filepicker relative w-full">
                        <button
                            class="mb-3 flex w-full items-center gap-5 text-sm text-heading-foreground"
                            type="button"
                        >
                            <span
                                class="inline-grid size-12 place-items-center rounded-full bg-foreground/[7%] text-heading-foreground transition-colors group-hover/filepicker:bg-heading-foreground group-hover/filepicker:text-heading-background"
                            >
                                <x-tabler-plus />
                            </span>
                            @lang('Announcement Image (Dark)')
                        </button>
                        <img
                            class="h-48 w-48 object-cover"
                            src="{{ setting('announcement_image_dark', '/upload/images/speaker.png') }}"
                            alt=""
                        />
                        <x-forms.input
                            class="absolute inset-0 z-2 h-full w-full cursor-pointer opacity-0"
                            class:label="leading-tight text-foreground/30"
                            id="announcement_image_dark"
                            container-class="static max-w-[270px] mx-auto"
                            size="lg"
                            name="announcement_image_dark"
                            type="file"
                            placeholder="{{ __('Upload Announcement Image') }}"
                        />

                    </div>
                    <div class="group/filepicker relative">
                        <button
                            class="mb-3 flex w-full items-center gap-5 text-sm text-heading-foreground"
                            type="button"
                        >
                            <span
                                class="inline-grid size-12 place-items-center rounded-full bg-foreground/[7%] text-heading-foreground transition-colors group-hover/filepicker:bg-heading-foreground group-hover/filepicker:text-heading-background"
                            >
                                <x-tabler-plus />
                            </span>
                            @lang('Announcement Background Image Dark (Optional)')
                        </button>
                        @if (setting('announcement_background_image_dark'))
                            <img
                                class="mb-4 h-48 w-48 object-cover"
                                src="{{ setting('announcement_background_image_dark', '') }}"
                                alt=""
                            />
                        @endif
                        <x-forms.input
                            class="absolute inset-0 z-2 h-full w-full cursor-pointer opacity-0"
                            class:label="leading-tight text-foreground/30"
                            id="announcement_background_image_dark"
                            container-class="static max-w-[270px] mx-auto"
                            size="lg"
                            name="announcement_background_image_dark"
                            type="file"
                            placeholder="{{ __('Upload Announcement Background Image (Dark)') }}"
                        />
                    </div>
                    <x-forms.input
                        class="w-full"
                        id="announcement_background_color_dark"
                        label="{{ __('Announcement Background Color (Dark)') }}"
                        placeholder="{{ __('Background Color (Dark)') }}"
                        name="announcement_background_color_dark"
                        value="{{ setting('announcement_background_color_dark', '#1a1d23') }}"
                        type="color"
                    />
                </x-card>
            </div>

            @if ($app_is_demo)
                <x-button
                    type="button"
                    onclick="return toastr.info('This feature is disabled in Demo version.');"
                >
                    {{ __('Save') }}
                </x-button>
                <x-button
                    type="button"
                    variant="secondary"
                    onclick="return toastr.info('This feature is disabled in Demo version.');"
                >
                    {{ __('Reset All Data') }}
                </x-button>
                <x-button
                    type="button"
                    variant="secondary"
                    onclick="return toastr.info('This feature is disabled in Demo version.');"
                >
                    {{ __('Re-Notify All Users') }}
                </x-button>
            @else
                <x-button type="submit">
                    {{ __('Save') }}
                </x-button>
                <x-button
                    type="button"
                    onclick="resetAllData()"
                    variant="secondary"
                >
                    {{ __('Reset All Data') }}
                </x-button>
                <x-button
                    type="button"
                    onclick="notifyAllUsers()"
                    name="re_notify"
                    variant="secondary"
                >
                    {{ __('Re-Notify All Users') }}
                </x-button>
            @endif

        </form>

    </div>
@endsection

@push('script')
    <script>
        function notifyAllUsers() {
            $.ajax({
                url: '{{ route('dashboard.admin.announcements.re_notify') }}',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }

        function resetAllData() {
            $.ajax({
                url: '{{ route('dashboard.admin.announcements.reset') }}',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    </script>
@endpush
