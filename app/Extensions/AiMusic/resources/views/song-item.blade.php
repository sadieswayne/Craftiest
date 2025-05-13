@php
    $dataIndex = isset($loop) ? $loop->index : rand(10000, 90000);
@endphp

<x-card
    id="video-{{ $entry['music_id'] ?? $entry['id']}}"
    class="image-result group flex text-center shadow-[0_2px_2px_hsla(0,0%,0%,0.07)]"
    class:body="flex flex-col grow p-9"
    data-index="{{ $dataIndex }}"
    data-video-src="{{ $entry['audio_url'] ?? '' }}"
    x-ref="image-result-{{ $dataIndex }}"
>
    @if ($entry['status'] == 'complete')

        <h4 class="mb-2.5 text-sm">
            {{ $entry['video_name'] ?? __('Generated Song') }}
        </h4>
        <p class="text-2xs font-medium opacity-60">
            @lang('Created') {{ \Carbon\Carbon::parse($entry['created_at'])->diffForHumans() }}
        </p>

        <div class="lqd-image-result-actions mt-auto flex w-full items-center justify-center gap-3">
            <x-button
                class="lqd-image-result-view gallery size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-emerald-400 hover:text-white"
                variant="ghost-shadow"
                size="none"
                href="#"
                @click.prevent="setVideoSrc('{{ $entry['audio_url'] ?? '' }}'); setActiveIndex({ index: {{ $dataIndex }} })"
            >
                <x-tabler-player-play class="size-4"/>
            </x-button>
            <x-button
                class="lqd-image-result-download download size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-emerald-400 hover:text-white"
                variant="ghost-shadow"
                size="none"
                download="{{ $entry['title'] ?? __('Unknown') }}"
                href="{{ $entry['audio_url'] ?? '' }}"
                :disabled="!isset($entry['audio_url'])"
            >
                <x-tabler-circle-chevron-down class="size-5"/>
            </x-button>
            <x-button
                class="lqd-image-result-delete delete size-9 rounded-full bg-background text-foreground hover:bg-background hover:bg-red-500 hover:text-white"
                variant="ghost-shadow"
                size="none"
                onclick="return confirm('{{ __('Are you sure? This action is permanent and will delete the Tavus related document for the user.') }}')"
                :href="LaravelLocalization::localizeUrl(route('dashboard.user.ai-music.delete', $entry['music_id'] ?? $entry['id']))"
            >
                <x-tabler-x class="size-4"/>
            </x-button>
        </div>
    @else
        <div class="px-5 py-9">
            <svg
                class="size-7 mx-auto mb-3 animate-spin"
                width="28"
                height="28"
                viewBox="0 0 28 28"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M14.0013 27.3333C6.65464 27.3333 0.667969 21.3467 0.667969 14C0.667969 11.5067 1.3613 9.08 2.66797 6.97333C3.05464 6.34667 3.8813 6.16 4.50797 6.54667C5.13464 6.93333 5.3213 7.75999 4.93464 8.38665C3.89464 10.0667 3.33464 12.0133 3.33464 14C3.33464 19.88 8.1213 24.6667 14.0013 24.6667C19.8813 24.6667 24.668 19.88 24.668 14C24.668 8.12 19.8813 3.33333 14.0013 3.33333C13.268 3.33333 12.668 2.73333 12.668 2C12.668 1.26667 13.268 0.666666 14.0013 0.666666C21.348 0.666666 27.3346 6.65333 27.3346 14C27.3346 21.3467 21.348 27.3333 14.0013 27.3333Z"
                    fill="url(#loader-spinner-gradient)"
                />
            </svg>
            <span class="inline-block bg-gradient-to-r from-[#82E2F4] to-[#6977DE] bg-clip-text text-sm font-semibold text-transparent">
                @lang('In Progress')
            </span>
        </div>
    @endif
</x-card>
