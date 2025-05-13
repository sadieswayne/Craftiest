@php
    $favoriteChabots = cache('favorite_chatbots');
@endphp

<x-card
    class="flex w-full flex-col lg:w-[48%]"
    id="favorite-chatbots"
    size="md"
>
    <x-slot:head
        @class(['pb-0 pt-5 border-0' => filled($favoriteChabots)])
    >
        <div class="flex items-center justify-between">
            <h4 class="m-0 text-[17px]">{{ __('Favorite Chatbots') }}</h4>
            <x-button
                variant="link"
                href="{{ \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot') ? route('dashboard.chatbot.index') : '#' }}"
            >
                <span class="text-nowrap font-bold text-foreground"> {{ __('View All') }} </span>
                <x-tabler-chevron-right class="ms-auto size-4" />
            </x-button>
        </div>
    </x-slot:head>
    <div class="flex w-full flex-wrap justify-between gap-y-4">
        @forelse ($favoriteChabots as $chatbot)
            <x-card
                class="flex w-full flex-col lg:w-[48%]"
                class:body="space-y-3"
                size="sm"
            >
                <img
                    class="rounded-full max-sm:mx-auto"
                    width="61"
                    height="61"
                    src="{{ asset($chatbot->avatar) }}"
                    alt=""
                >
                <h4 class="font-medium max-sm:text-center">{{ $chatbot->title }}</h4>
                <div class="flex w-fit rounded-xl border px-2 py-1 max-sm:mx-auto">
                    <span class="text-center">{{ ucwords(str_replace('_', ' ', $chatbot->interaction_type->value)) }}</span>
                </div>
            </x-card>
        @empty
            <h3 class="mx-auto">
                {{ __("You don't have favorite chatbot") }}
            </h3>
        @endforelse
    </div>
</x-card>
