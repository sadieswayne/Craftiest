@extends('panel.user.openai_chat.chat')
@section('title', __('AI Web Chat'))
@section('titlebar_subtitle', __('Share a web link, and let the chatbot delve into the content, providing relevant insights and opinions.'))
@section('titlebar_actions', '')

@section('chat_sidebar_actions', '')

@section('openChatAreaContainerUrl', '/dashboard/user/openai/webchat/open-chat-area-container')

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
    </form>
@endsection


@push('script')
    <script src="{{ asset('vendor/web-chat/js/openai_webchat.js') }}"></script>
@endpush



