<div
    class="hidden"
    :class="{ 'hidden': activeTab !== 'text' }"
>
    <form
        class="flex flex-col gap-5"
        id="add-form"
        action="{{ route('dashboard.admin.chatbot.text', $item) }}"
    >
        @csrf

        <x-form-step
            step="1"
            label="{{ __('Add Text') }}"
        />

        <input
            id="text_id"
            type="hidden"
            name="text_id"
            value=""
        >

        <x-forms.input
            id="title"
            name="title"
            size="lg"
            placeholder="{{ __('Type your title here') }}"
            label="{{ __('Title') }}"
        />

        <x-forms.input
            id="content_text"
            name="text"
            placeholder="{{ __('Type your text here') }}"
            label="{{ __('Text') }}"
            rows="6"
            type="textarea"
            size="lg"
        />

        <x-button
            class="group w-full"
            data-submit="addtext"
            data-form="#add-form"
            data-list="#text-list"
            variant="success"
            type="button"
        >
            <x-tabler-refresh class="size-4 hidden animate-spin group-[&.lqd-is-busy]:block" />
            <x-tabler-plus class="size-4 group-[&.lqd-is-busy]:hidden" />
            @lang('Add')
        </x-button>

    </form>

    <form
        class="mt-10 flex flex-col gap-5"
        id="form-train-text"
        method="post"
        action="{{ route('dashboard.admin.chatbot.training', $item->id) }}"
    >
        @php
            $texts = $data->where('type', 'text');
        @endphp

        <input
            type="hidden"
            name="type"
            value="text"
        >
        <x-form-step
            id="text-list-alert"
            step="2"
            label="{{ __('Manage Content') }}"
            @class(['text-list-alert', 'hidden' => !$texts->count()])
        />

        <div
            class="text-list space-y-4"
            id="text-list"
        >
            @include('panel.admin.chatbot.particles.text.list', ['items' => $texts])
        </div>
    </form>
</div>
