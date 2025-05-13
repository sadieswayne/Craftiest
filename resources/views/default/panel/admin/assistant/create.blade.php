@php
    $tabs = [
        'assistant' => [
            'title' => __('Assistant'),
        ],
        'pdf' => [
            'title' => __('PDF, XLSX, CSV'),
        ],
        'tools' => [
            'title' => __('Tools'),
        ],
    ];
@endphp

@extends('panel.layout.settings')
@section('title', __("Assistant Training"))
@section('titlebar_actions', '')

@section('settings')

    <p class="mb-9 font-medium">
        @lang('Simply select the source and MagicAI will do the rest to train your GPT in seconds.')
    </p>

    <div x-data="{
        activeTab: 'assistant',
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
            <form
                    action="{{route("dashboard.admin.ai-assistant.store")}}"
                    method="POST"
                    enctype="multipart/form-data"
                    @submit="Alpine.store('appLoadingIndicator').show()">
                @csrf

                @include('panel.admin.assistant.particles.assistant')
                @include('panel.admin.assistant.particles.pdf-tab')
                @include('panel.admin.assistant.particles.tools')

                <x-button
                        id="upload-button"
                        class="!mt-8 w-full"
                        data-submit="train"
                        data-form="#form-train-web-site"
                        data-list="#chatbotPages"
                        size="lg"
                        type="submit"
                >
                    @lang('Assistant Training')
                </x-button>
            </form>
            <x-loader/>
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
