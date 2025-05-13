@extends('panel.user.openai_chat.components.chat_area_container')

@section('chat_head_actions')
    <form
            class="relative -order-1 flex items-center justify-end gap-3"
            id="start-analyze-form"
            @submit.prevent="startAnalyze"
            action="#"
    >
        <input
                type="hidden"
                name="category_id"
                value="{{ $category->id }}"
        />
        <input
                type="hidden"
                name="locale"
                value="{{ LaravelLocalization::getCurrentLocale() }}"
        />
        <input
                type="hidden"
                name="createChatUrl"
                id="createChatUrl"
                value="/dashboard/user/openai/webchat/start-new-chat"
        />
        <x-forms.input
                class="lg:min-w-96 w-full rounded-full"
                id="website_url"
                type="text"
                name="website_url"
                placeholder="{{ __('Input website url to analyze') }}"
        />
        <x-button
                class="bg-background"
                id="start_analyze_btn"
                variant="outline"
                hover-variant="primary"
                type="submit"
        >
            <x-tabler-zoom-scan class="size-4 md:hidden" />
            <span class="hidden md:block">
                {{ __('Analyze') }}
            </span>
        </x-button>
@endsection