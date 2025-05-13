@php
    $base_class = 'lqd-modal lqd-modal-' . $type . ' relative';
    $modal_base_class = 'lqd-modal-modal z-[999] flex items-center justify-center overflow-y-auto overscroll-contain';
    $modal_backdrop_base_class = 'lqd-modal-backdrop fixed inset-0 bg-black/5 backdrop-blur-sm';
    $modal_head_base_class = 'lqd-modal-head flex flex-wrap items-center gap-3 border-b px-4 py-2 relative';
    $modal_body_base_class = 'lqd-modal-body p-10';
    $modal_content_base_class =
        'lqd-modal-content relative z-[100] max-h-[95vh] min-w-[min(calc(100%-2rem),540px)] rounded-xl bg-background shadow-2xl shadow-black/10 overflow-y-auto';
    $modal_close_btn_base_class = 'lqd-modal-close size-8 ms-auto inline-flex items-center justify-center rounded-lg transition-all hover:bg-foreground/20';

    if ($type !== 'inline') {
        $modal_base_class .= ' fixed inset-0';
    } else {
        $modal_base_class .= ' hidden fixed max-md:inset-0 md:absolute top-full min-w-[min(calc(100vw-2rem),450px)] mt-3';

        if ($anchor === 'start') {
            $modal_base_class .= ' start-0';
        } else {
            $modal_base_class .= ' end-0';
        }
    }

    if ($type === 'page') {
        $modal_base_class .= ' ';
        $modal_content_base_class .= ' max-h-screen bg-transparent shadow-none w-[clamp(100%,1440px,90vw)]';
        $modal_head_base_class .= ' border-none p-0 w-[clamp(100%,1440px,90vw)]';
        $modal_body_base_class .= ' py-24 px-0';
        $modal_close_btn_base_class .= ' absolute top-11 end-0 lg:end-11 bg-background text-heading-foreground size-14 rounded-full hover:bg-background hover:scale-110';
    }
@endphp

<div
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    x-data="{
        modalOpen: false,
        toggleModal() {
            @if ($disableModal) toastr.info( '{{ $disableModalMessage }}' )
			@else
			this.modalOpen = !this.modalOpen @endif
        }
    }"
>
    @if (!empty($trigger))
        @if ($trigger->attributes['custom'] ?? false)
            {{ $trigger }}
        @else
            <x-button
                class="{{ $trigger->attributes['class'] }}"
                :attributes="$trigger->attributes"
                href="{{ $trigger->attributes['href'] }}"
                variant="{{ $trigger->attributes['variant'] }}"
                size="{{ $trigger->attributes['size'] }}"
                @click.prevent="toggleModal()"
            >
                {{ $trigger }}
            </x-button>
        @endif
    @endif
    @if (!empty($modal) && !$disableModal)
        @if ($type !== 'inline')
            <template x-teleport="body">
        @endif
        <div
            {{ $modal->attributes }}
            {{ $attributes->twMergeFor('modal', $modal_base_class) }}
            x-show="modalOpen"
            x-transition
            @keyup.escape="modalOpen = false"
            :class="{ 'hidden': !modalOpen }"
            @if (!$disableFocus) x-trap="modalOpen" @endif
        >
            <div {{ $attributes->twMergeFor('modal-backdrop', $modal_backdrop_base_class) }}></div>

            <div
                {{ $attributes->twMergeFor('modal-content', $modal_content_base_class) }}
                @click.outside="modalOpen = false"
            >
                @if ($type === 'page')
                    <div {{ $attributes->twMergeFor('modal-container', 'container px-0') }}>
                @endif
                @if ($type !== 'inline')
                    <div {{ $attributes->twMergeFor('modal-head', $modal_head_base_class) }}>
                        @if (!empty($title))
                            <h4 class="my-0">{{ $title }}</h4>
                        @endif
                        @if (!empty($headContent))
                            {{ $headContent }}
                        @endif

                        <button
                            {{ $attributes->twMergeFor('close-btn', $modal_close_btn_base_class) }}
                            type="button"
                            @click.prevent="modalOpen = false"
                        >
                            <x-tabler-x class="size-5" />
                        </button>
                    </div>
                @endif
                @if ($type === 'page')
            </div>
    @endif

    <div {{ $attributes->twMergeFor('modal-body', $modal_body_base_class) }}>
        <div {{ $attributes->twMergeFor('modal-container', 'container p-0') }}>
            {{ $modal }}
        </div>
    </div>
</div>
</div>
@if ($type !== 'inline')
    </template>
@endif
@endif
</div>
