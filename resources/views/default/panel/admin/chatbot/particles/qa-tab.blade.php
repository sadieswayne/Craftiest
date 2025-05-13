<div
    class="hidden"
    :class="{ 'hidden': activeTab !== 'qa' }"
>
    <form
        class="flex flex-col gap-5"
        id="add-qa-form"
        action="{{ route('dashboard.admin.chatbot.qa', $item) }}"
    >
        @csrf

        <x-form-step
            step="1"
            label="{{ __('Add Q/A') }}"
        />

        <input
            id="qa_id"
            type="hidden"
            name="qa_id"
            value=""
        >

        <x-forms.input
            id="question"
            name="question"
            placeholder="{{ __('Type your question here') }}"
            label="{{ __('Question') }}"
            size="lg"
        />

        <x-forms.input
            id="answer"
            name="answer"
            placeholder="{{ __('Type your answer here') }}"
            label="{{ __('Answer') }}"
            rows="6"
            type="textarea"
            size="lg"
        />

        <x-button
            class="group w-full"
            data-submit="addqa"
            data-form="#add-qa-form"
            data-list="#text-list"
            type="button"
            variant="success"
        >
            <x-tabler-refresh class="size-4 hidden animate-spin group-[&.lqd-is-busy]:block" />
            <x-tabler-plus class="size-4 group-[&.lqd-is-busy]:hidden" />
            @lang('Add')
        </x-button>

    </form>

    <form
        class="mt-10 flex flex-col gap-5"
        id="form-train-qa"
        method="post"
        action="{{ route('dashboard.admin.chatbot.training', $item->id) }}"
    >
        @php
            $qa = $data->where('type', 'qa');
        @endphp

        <input
            type="hidden"
            name="type"
            value="qa"
        >

        <x-form-step
            id="text-list-alert"
            step="2"
            label="{{ __('Manage Content') }}"
            @class(['text-list-alert', 'hidden' => !$qa->count()])
        />

        <div
            class="qa-list space-y-4"
            id="qa-list"
        >
            @include('panel.admin.chatbot.particles.qa.list', ['items' => $qa])
        </div>
    </form>
</div>
