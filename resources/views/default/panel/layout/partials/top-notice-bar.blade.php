@php
    $text = "We've revamped the plan management system to give you full control over your pricing strategies. You may need to review and update your pricing plans.";
    $btn_text = "See What's New";
@endphp

@auth
    @if (auth()->user()->isAdmin())
        <x-alert
            class="top-notice-bar top-notice-bar-visible items-center rounded-none py-1 text-xs shadow-none lg:h-[--top-notice-bar-height]"
            id="top-notice-bar"
            variant="warn-fill"
            icon="tabler-info-circle"
            size="xs"
            x-data="{ noticeBarHidden: localStorage.getItem('lqdTopBarNotice') === 'hidden' }"
            ::class="{ 'hidden': noticeBarHidden, 'top-notice-bar-hidden': noticeBarHidden, 'top-notice-bar-visible': !noticeBarHidden }"
        >
            <script>
                if (localStorage.getItem('lqdTopBarNotice') === 'hidden') {
                    document.getElementById('top-notice-bar').classList.add('top-notice-bar-hidden');
                    document.getElementById('top-notice-bar').classList.remove('top-notice-bar-visible');
                    document.getElementById('top-notice-bar').style.display = 'none';
                }
            </script>
            <div class="flex w-full grow items-center justify-between gap-2">
                <p
                    class="m-0 w-full lg:overflow-hidden lg:text-ellipsis lg:whitespace-nowrap"
                    title="@lang($text)"
                >
                    @lang($text)
                </p>
                <x-button
                    class="shrink-0 bg-background px-2.5 py-1 text-xs text-heading-foreground hover:bg-primary hover:text-primary-foreground"
                    size="sm"
                    @click="localStorage.setItem('lqdTopBarNotice', 'hidden'); noticeBarHidden = true;"
                    href="{{ route('dashboard.admin.finance.plan.index') }}"
                >
                    @lang($btn_text)
                </x-button>
            </div>
        </x-alert>
    @endif
@endauth
