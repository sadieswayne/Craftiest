@php
    $base_class = 'lqd-total-docs flex flex-col gap-4';
@endphp

<div {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}>
    <div>
        <div class="lqd-progress flex h-2 overflow-hidden rounded-full">
            @if ($total_documents != 0)
                <div
                    class="lqd-progress-bar shrink-0 grow-0 basis-auto bg-primary"
                    style="width: {{ (100 * (int) $total_text_documents) / (int) $total_documents }}%"
                ></div>
            @endif
            @if ($setting->feature_ai_image && $total_documents != 0)
                <div
                    class="lqd-progress-bar shrink-0 grow-0 basis-auto bg-secondary"
                    style="width: {{ (100 * (int) $total_image_documents) / (int) $total_documents }}%"
                ></div>
            @endif
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-5">
        <x-legend label="{{ __('Text') }}">
            <span class="ms-auto">
                {{ $total_text_documents }}
            </span>
        </x-legend>
        @if ($setting->feature_ai_image)
            <x-legend
                class:box="bg-secondary"
                label="{{ __('Image') }}"
            >
                <span class="ms-auto">
                    {{ $total_image_documents }}
                </span>
            </x-legend>
        @endif
        <x-legend
            class:box="bg-foreground/10"
            label="{{ __('Total') }}"
        >
            <span class="ms-auto">
                {{ $total_documents }}
            </span>
        </x-legend>
    </div>
</div>
