<div class="lqd-chats-list grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach ($aiList as $entry)
        <x-card
            class:body="static"
            id="{{ $entry->id }}"
            data-filter="{{ $entry->category }}"
            data-title="{{ str()->lower($entry->name) }}"
            data-description="{{ str()->lower($entry->description) }}"
            data-favorite="{{ $favData->contains('item_id', $entry->id) ? 'true' : 'false' }}"
            @class([
                'lqd-chat-item group relative w-full px-16 py-8 pt-12 max-xl:px-10',
                'border-t-0 border-s-0 border-b border-e' =>
                    Theme::getSetting('defaultVariations.card.variant', 'outline') ===
                    'outline',
            ])
            roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
            size="none"
            x-init="true"
            ::class="{
                'hidden': ($store.chatsFilter.searchStr !== '' || $store.chatsFilter.filter !== 'all') &&
                    (!$el.getAttribute('data-title').includes($store.chatsFilter.searchStr) && !$el.getAttribute('data-description').includes($store.chatsFilter.searchStr)) ||
                    ($store.chatsFilter.filter !== 'all' && ($store.chatsFilter.filter === 'favorite' && $el.getAttribute('data-favorite') !== 'true')) ||
                    (($store.chatsFilter.filter !== 'all' && $store.chatsFilter.filter !== 'favorite') && $store.chatsFilter.filter !== $el.getAttribute('data-filter'))
            }"
        >
            <!-- link to the chat -->
            <a
                @class([
                    'absolute left-0 top-0 z-2 block h-full w-full',
                    'border-[3px] border-secondary' => $entry->plan == 'premium',
                ])
                href="{{  (route('dashboard.user.openai.chat.chat', $entry->slug)) }}"
            ></a>

            <x-favorite-button
                class="absolute start-4 top-4"
                id="{{ $entry->id }}"
                is-favorite="{{ $favData->contains('item_id', $entry->id) }}"
                update-url="/dashboard/admin/openai/chat/update-fav"
                @click="$el.parentElement.setAttribute('data-favorite',  $el.parentElement.getAttribute('data-favorite') === 'true' ? 'false' : 'true')"
            />

            @if ($entry->plan == 'premium')
                <span class="absolute end-4 top-4 inline-flex items-center gap-1 rounded-md bg-secondary p-2 text-2xs font-medium leading-tight text-secondary-foreground">
                    {{-- blade-formatter-disable --}}
                    <svg width="19" height="15" viewBox="0 0 19 15" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path d="M7.75 7.5002L6 5.5752L6.525 4.7002M4.25 1.375H14.75L17.375 5.75L9.9375 14.0625C9.88047 14.1207 9.8124 14.1669 9.73728 14.1985C9.66215 14.2301 9.58149 14.2463 9.5 14.2463C9.41851 14.2463 9.33785 14.2301 9.26272 14.1985C9.1876 14.1669 9.11953 14.1207 9.0625 14.0625L1.625 5.75L4.25 1.375Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
					{{-- blade-formatter-enable --}}
                    {{ __('Premium') }}
                </span>
            @endif

            <div class="lqd-chat-item-details relative flex flex-col justify-center text-center">
                <div
                    class="lqd-chat-item-avatar text-black/65 mx-auto mb-6 inline-flex h-32 w-32 items-center justify-center overflow-hidden overflow-ellipsis whitespace-nowrap rounded-full border-[6px] border-solid border-white/90 text-5xl font-medium shadow-[0_1px_2px_rgba(0,0,0,0.07)] transition-shadow group-hover:shadow-xl dark:border-current"
                    style="background: {{ $entry->color }};"
                >
                    @if ($entry->slug === 'ai-chat-bot')
                        <img
                            class="lqd-chat-avatar-img h-full w-full object-cover object-center"
                            src="{{ custom_theme_url('/assets/img/chat-default.jpg') }}"
                            alt="{{ __($entry->name) }}"
                        >
                    @elseif ($entry->image)
                        <img
                            class="lqd-chat-avatar-img h-full w-full object-cover object-center"
                            src="{{ custom_theme_url($entry->image, true) }}"
                            alt="{{ __($entry->name) }}"
                        >
                    @else
                        <span class="block w-full overflow-hidden overflow-ellipsis whitespace-nowrap text-center">
                            {{ __($entry->short_name) }}
                        </span>
                    @endif
                </div>
                <div class="lqd-chat-item-info">
                    <h3 class="lqd-chat-item-title mb-1 text-lg">
                        {{ __($entry->name) }}
                    </h3>
                    <p class="lqd-chat-item-desc mb-0 opacity-80">
                        {{ __($entry->description) }}
                    </p>
                </div>
            </div>
        </x-card>
    @endforeach
</div>
