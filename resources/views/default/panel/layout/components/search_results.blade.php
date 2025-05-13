@php
    $auth = Auth::user();
    $plan = $auth->activePlan();
    $plan_type = 'regular';
    $upgrade = false;
@endphp
@if (count($template_search) > 0)
    <ul class="font-medium">
        @foreach ($template_search as $item)
            @php
                if ($app_is_demo) {
                    if ($item->premium == 1 && $plan_type === 'regular') {
                        $upgrade = true;
                    }
                } else {
                    if (!$auth->isAdmin() && $item->premium == 1 && $plan_type === 'regular') {
                        $upgrade = true;
                    }
                }
                if ($upgrade) {
                    $href = 'dashboard.user.payment.subscription';
                } else {
                    $href = 'dashboard.user.openai.generator.workbook';
                    switch ($item->type) {
                        case 'image':
                        case 'video':
                        case 'audio':
                        case 'isolator':
                        case 'voiceover':
                            $href = 'dashboard.user.openai.generator';
                            break;
                    }
                    switch ($item->slug) {
                        case 'ai_webchat':
                            $href = 'dashboard.user.openai.webchat.workbook';
                            break;
                        case 'ai_webchat':
                            $href = 'dashboard.user.openai.rewrite';
                            break;
                    }
                }
            @endphp
            <li class="border-b px-3 py-2 transition-colors last:border-b-0 hover:bg-foreground/5">
                <a
                    class="flex items-center gap-2 text-heading-foreground"
                    href="{{  (route($href, $item->slug)) }}"
                >
                    <x-lqd-icon
                        size="lg"
                        style="background: {{ $item->color }}"
                    >
                        <span class="size-5 flex">
                            @if ($item->image !== 'none')
                                {!! html_entity_decode($item->image) !!}
                            @endif

                            @if ($item->active == 1)
                                <span class="size-3 absolute bottom-0 end-0 inline-block rounded-full border-2 border-background bg-green-500"></span>
                            @else
                                <span class="size-3 absolute bottom-0 end-0 inline-block rounded-full border-2 border-background bg-red-500"></span>
                            @endif
                        </span>
                    </x-lqd-icon>
                    {{ $item->title }}
                    <small class="ms-auto text-foreground/50">{{ __('Template') }}</small>
                </a>
            </li>
        @endforeach
    </ul>
@endif

@if (count($ai_chat_search) > 0)
    <ul class="font-medium">
        @foreach ($ai_chat_search as $item)
            <li class="border-b px-3 py-2 transition-colors last:border-b-0 hover:bg-foreground/10">
                <a
                    class="flex items-center gap-2 text-heading-foreground"
                    href="{{  (route('dashboard.user.openai.chat.chat', $item->slug)) }}"
                >
                    <x-lqd-icon
                        size="lg"
                        style="background: {{ $item->color }}"
                    >
                        <span class="size-5 flex">
                            @if ($item->slug == 'ai-chat-bot')
                                <x-tabler-messages class="size-5" />
                            @else
                                {{ $item->short_name }}
                            @endif
                        </span>
                    </x-lqd-icon>
                    {{ $item->name }}
                    <small class="ms-auto text-foreground/50">{{ __('AI Chat Template') }}</small>
                </a>
            </li>
        @endforeach
    </ul>
@endif

@if (count($workbook_search) > 0)
    <hr class="border-t-2">
    <h3 class="m-0 border-b px-3 py-3 text-base font-medium">{{ __('Workbooks') }}</h3>
    <ul>
        @foreach ($workbook_search as $item)
            <li class="border-b px-3 py-2 transition-colors last:border-b-0 hover:bg-foreground/5">
                <a
                    class="flex items-center gap-2 text-heading-foreground"
                    href="{{  (route('dashboard.user.openai.documents.single', $item->slug)) }}"
                >
                    <x-lqd-icon
                        size="lg"
                        style="background: {{ $item->color }}"
                    >
                        <span class="size-5 flex">
                            @if ($item->image !== 'none')
                                {!! html_entity_decode($item->image) !!}
                            @endif
                        </span>
                    </x-lqd-icon>
                    {{ $item->title }}
                    <small class="ms-auto text-foreground/50">{{ __('Workbook') }}</small>
                </a>
            </li>
        @endforeach
    </ul>
@endif

@if (isset($result) and $result == 'null')
    <div class="p-6 text-center font-medium text-heading-foreground">
        <h3 class="mb-2">{{ __('No results.') }}</h3>
        <p class="opacity-70">{{ __('Please try with another word.') }}</p>
    </div>
@endif
