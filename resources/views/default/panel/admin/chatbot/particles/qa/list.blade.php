@foreach ($items as $item)
    <div
        class="item flex items-center justify-between rounded-lg border p-1.5"
        id="div-item-{{ $item->id }}"
    >
        <x-forms.input
            class:container="grow"
            class:custom-wrap="size-7"
            id="item_{{ $item->id }}"
            :checked="$item->status == 'trained'"
            name="chatbot_data[]"
            type="checkbox"
            value="{{ $item->id }}"
            label="{{ $item->getAttribute('type_value') }}"
            custom
        />

        <div class="flex items-center justify-between gap-1">
            <x-badge
                class="text-2xs"
                variant="{{ $item->status == 'waiting' ? 'secondary' : 'success' }}"
            >
                {{ $item->status }}
            </x-badge>
            <x-button
                class="size-7 inline-flex items-center justify-center"
                data-edit="item-qa"
                data-question="{{ $item->type_value }}"
                data-answer="{{ $item->content }}"
                data-id="{{ $item->id }}"
                variant="outline"
                size="none"
            >
                <x-tabler-pencil class="size-5" />
                <span class="sr-only">{{ __('Edit') }}</span>
            </x-button>
            <x-button
                class="inline-flex items-center justify-center text-red-600"
                data-url="{{ route('dashboard.admin.chatbot.item.delete', [$item->chatbot_id, $item->id]) }}"
                data-item="delete"
                data-parent="#div-item-{{ $item->id }}"
                variant="link"
                size="none"
            >
                <x-tabler-circle-minus
                    class="size-7"
                    stroke-width="1.5"
                />
                <span class="sr-only">{{ __('Delete') }}</span>
            </x-button>
        </div>
    </div>
@endforeach

@if ($items->count())
    <x-button
        class="!mt-8 w-full"
        data-submit="train"
        data-form="#form-train-qa"
        data-list="#qa-list"
        type="button"
        size="lg"
    >
        @lang('Train GPT')
    </x-button>
@endif
