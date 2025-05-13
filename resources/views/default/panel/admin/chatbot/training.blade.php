@php
    $tabs = [
        'website' => [
            'title' => __('Website'),
        ],
        'pdf' => [
            'title' => __('PDF, XLSX, CSV'),
        ],
        'text' => [
            'title' => __('Text'),
        ],
        'qa' => [
            'title' => __('Q&A'),
        ],
    ];
@endphp

@extends('panel.layout.settings')
@section('title', $title)
@section('titlebar_actions', '')

@section('settings')
    <form
        class="mb-4 flex flex-wrap items-center justify-between gap-2"
        id="save_form"
        method="post"
        action="{{ $action }}"
        x-data="{ title: '{{ old('title', $item->title) }}' }"
        x-ref="form"
    >
        @method('PUT')
        @csrf

        <input
            type="hidden"
            name="title"
            value="{{ old('title', $item->title) }}"
            :value="title"
        >
        <div class="flex grow items-center gap-2">
            <p
                class="m-0 text-2xl font-bold text-heading-foreground"
                x-ref="titlePreview"
                contenteditable="true"
                @input="title = $event.target.innerText"
                @keydown.enter.prevent="$refs.form.requestSubmit()"
            >
                {{ old('title', $item->title) }}
            </p>
            @error('title')
                <p class="text-red-500">
                    {{ $message }}
                </p>
            @enderror
            {{-- <x-button
                class="size-9 shrink-0 shadow-xl"
                @click.prevent="$refs.titlePreview.focus();"
                size="none"
                variant="ghost-shadow"
            >
                <x-tabler-pencil />
                <span class="sr-only">
                    {{ __('Edit') }}
                </span>
            </x-button> --}}
            <button
                @class([
                    'chat-item-update-title' => !$app_is_demo,
                    'flex size-7 items-center relative z-1 justify-center rounded-full border bg-background transition-all dark:bg-primary dark:border-primary hover:scale-110 group-[&.edit-mode]:bg-emerald-500 group-[&.edit-mode]:border-emerald-500 group-[&.edit-mode]:text-white',
                ])
                @if ($app_is_demo) onclick="return toastr.info({{ __('This feature is disabled in Demo version.') }})" @endif
            >
                <x-tabler-pencil class="size-4 group-[&.edit-mode]:hidden" />
                <span class="sr-only">
                    {{ __('Edit') }}
                </span>
                <x-tabler-check class="size-4 hidden group-[&.edit-mode]:block" />
            </button>
        </div>

        <div>
            <a
                class="flex items-center gap-1 underline decoration-heading-foreground/20 decoration-dotted underline-offset-4"
                href="#"
            >
                @lang('Need help?')
                <x-info-tooltip
                    text="{{ __('You can deploy your trained chatbot to an existing AI Chat template. Simply navigate to Chat Templates select Edit Template, and assign your chatbot there.') }}"
                />
            </a>
        </div>
    </form>

    <p class="mb-9 font-medium">
        @lang('Simply select the source and MagicAI will do the rest to train your GPT in seconds.')
    </p>

    <div x-data="{
        activeTab: 'website',
        setActiveTab(tab) {
            if (this.activeTab === tab) return;
            this.activeTab = tab;
        }
    }">
        <nav class="mb-14 flex flex-col justify-between gap-2 rounded-xl bg-foreground/5 px-2.5 py-1.5 font-medium leading-snug sm:flex-row sm:rounded-full">
            @foreach ($tabs as $tab => $tabData)
                <button
                    @class([
                        'rounded-xl px-5 grow py-2.5 text-foreground transition-colors hover:bg-foreground/5 [&.lqd-is-active]:bg-white [&.lqd-is-active]:text-black [&.lqd-is-active]:shadow-[0_2px_13px_rgba(0,0,0,0.1)] sm:rounded-full',
                        'lqd-is-active' => $loop->first,
                    ])
                    type="button"
                    @click="setActiveTab('{{ $tab }}')"
                    :class="{ 'lqd-is-active': activeTab === '{{ $tab }}' }"
                >
                    @lang($tabData['title'])
                </button>
            @endforeach
        </nav>

        <div>
            @include('panel.admin.chatbot.particles.web-site-tab')
            @include('panel.admin.chatbot.particles.pdf-tab')
            @include('panel.admin.chatbot.particles.text-tab')
            @include('panel.admin.chatbot.particles.qa-tab')
        </div>
    </div>

    <div class="crawler-spinner bg-background/65 fixed inset-0 z-50 mt-5 hidden text-center backdrop-blur-sm">
        <div class="container">
            <div class="flex min-h-screen flex-col items-center justify-center py-7">
                <div class="flex w-full flex-col items-center gap-11 md:w-5/12 lg:w-3/12">
                    <h5 class="text-lg">
                        @lang('Almost Done!')
                    </h5>
                    <x-tabler-loader-2
                        class="size-28 mx-auto animate-spin"
                        role="status"
                    />
                    <div class="space-y-3">
                        <p class="font-heading text-2xl font-bold text-heading-foreground">
                            @lang('Training GPT...')
                        </p>
                        <p>
                            @lang('We’re training your custom GPT with the related resources. This may take a few minutes.')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('assets/js/panel/admin.chatbot.js?v=' . time()) }}"></script>
@endpush
