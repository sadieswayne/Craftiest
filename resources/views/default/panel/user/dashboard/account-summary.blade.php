@php

    $docs = cache('user_docs');
    $docsCount = count($docs);
    $hoursSaved = $docsCount * 3;

    $textDocsCount = $docs->filter(fn($entity) => $entity->generator?->type == 'text')?->count();
    $imageDocsCount = $docs->filter(fn($entity) => $entity->generator?->type == 'image')?->count();
    $audioDocsCount = $docs->filter(fn($entity) => $entity->generator?->type == 'audio')?->count();
    $otherDocsCount = $docsCount - $textDocsCount - $imageDocsCount - $audioDocsCount;

    $sum = $textDocsCount + $imageDocsCount + $audioDocsCount;

    $chatbots = cache('user_chatbots');
    $chatbotCount = count($chatbots);

@endphp

<x-card class="w-full" id="summary" size="lg">
    <div class="flex justify-between sm:mb-7 max-sm:flex-wrap">
        <h3 class="inline-grid items-center leading-6 text-[17px] mb-0">
            @lang('Account Summary')
        </h3>
        <div class="flex justify-between w-full sm:w-1/2 max-sm:flex-wrap">
            <div
                class="flex relative justify-center flex-col grow sm:ps-12 sm:after:h-[80%] sm:after:w-px sm:after:bg-foreground/20 sm:after:absolute sm:after:right-0">
                <p class="text-nowrap leading-5 text-sm">@lang('Hours Saved')</p>
                <h2>{{ $hoursSaved }}</h2>
            </div>
            <div
                class="flex relative justify-center flex-col grow sm:ps-12 sm:after:h-[80%] sm:after:w-px sm:after:bg-foreground/20 sm:after:absolute sm:after:right-0">
                <p class="text-nowrap leading-5 text-sm">@lang('Documents')</p>
                <h2> {{ $docsCount }} </h2>
            </div>
            <div class="flex justify-center flex-col grow sm:ps-12">
                <p class="text-nowrap leading-5 text-sm">@lang('Chatbots')</p>
                <h2>{{ $chatbotCount }}</h2>
            </div>
        </div>
    </div>

    <hr>

    <div class="flex flex-col gap-4 sm:py-6">
        <h4 class="text-foreground/80">@lang('Document Overview')</h4>
        <div class="flex items-center max-sm:flex-wrap flex-nowrap max-sm:gap-4 gap-10">
            <div class="flex flex-nowrap gap-0.5 w-full h-[10px] rounded-lg overflow-hidden">
                <span class="bg-[#9A34CD] {{ $textDocsCount == 0 ? 'hidden' : '' }}"
                    style="width: {{ $sum == 0 ? '100' : ($textDocsCount / $sum) * 100 }}%"></span>
                <span class="bg-[#1CA685] {{ $imageDocsCount == 0 ? 'hidden' : '' }}"
                    style="width: {{ $sum == 0 ? '100' : ($imageDocsCount / $sum) * 100 }}%"></span>
                <span class="bg-[#667085] {{ $audioDocsCount == 0 ? 'hidden' : '' }}"
                    style="width: {{ $sum == 0 ? '100' : ($audioDocsCount / $sum) * 100 }}%"></span>
            </div>
            <x-button variant="link" href="{{ route('dashboard.user.openai.documents.all') }}">
                <span class="text-foreground font-bold text-nowrap">@lang('View All')</span>
                <x-tabler-chevron-right class="ms-auto size-4" />
            </x-button>
        </div>
        <div class="inline-flex flex-wrap max-sm:gap-3 gap-7 pt-1 px-2">
            <div class="inline-flex items-center gap-2">
                <span class="size-2.5 rounded-sm bg-[#9A34CD]"></span>
                <span class="text-heading-foreground text-sm leading-5">@lang('Text')</span>
                <span class="text-foreground/70 leading-5">{{ $textDocsCount }}</span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="size-2.5 rounded-sm bg-[#1CA685]"></span>
                <span class="text-heading-foreground text-sm leading-5">@lang('Image')</span>
                <span class="text-foreground/70 leading-5">{{ $imageDocsCount }}</span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="size-2.5 rounded-sm bg-[#667085]"></span>
                <span class="text-heading-foreground text-sm leading-5">@lang('Audio')</span>
                <span class="text-foreground/70 leading-5">{{ $audioDocsCount }}</span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="size-2.5 rounded-sm bg-[#E6E7E9]"></span>
                <span class="text-heading-foreground text-sm leading-5">@lang('Other')</span>
                <span class="text-foreground/70 leading-5">{{ $otherDocsCount }}</span>
            </div>
        </div>
    </div>
</x-card>
