@php
    $notifications = [];
    $unreadNotifications = auth()->user()->unreadNotifications;
    foreach ($unreadNotifications as $notification) {
        $notifications[] = [
            'title' => $notification->data['data']['title'],
            'message' => $notification->data['data']['message'],
            'link' => $notification->data['data']['link'],
            'unread' => true,
            'id' => $notification->id,
        ];
    }
    $trigger_class = @twMerge('size-6 max-lg:size-10 max-lg:border max-lg:dark:bg-white/[3%]', $attributes->get('class:trigger'));
@endphp

<div
    {{ $attributes->withoutTwMergeClasses()->twMerge('notifications-wrap group hidden md:flex') }}
    x-data="notifications({{ json_encode($notifications) }})"
    x-init="$store.notifications.setNotifications(notifications)"
    :class="{ 'has-unread': $store.notifications.hasUnread() }"
>
    <x-dropdown.dropdown
        {{ $attributes->twMergeFor('dropdown', 'notifications-dropdown') }}
        anchor="end"
        offsetY="26px"
    >
        <x-slot:trigger
            :class="$trigger_class"
            size="none"
        >
            <x-tabler-bell
                class="notifications-bell size-5 ![animation-iteration-count:3] [transform-origin:50%_5px] group-[&.has-unread]:animate-bell-ring"
                stroke-width="1.5"
            />
            <span
                class="notifications-ping-wrap absolute -end-0.5 -top-1 hidden size-2 rounded bg-red-500 group-[&.has-unread]:inline-block"
                title="{{ __('This Notification Is Unread') }}"
            >
                <div class="notifications-ping absolute inset-0 inline-block rounded-full bg-inherit ![animation-iteration-count:10] group-[&.has-unread]:animate-ping"></div>
            </span>
        </x-slot:trigger>

        <x-slot:dropdown
            class="max-h-96 w-80 overflow-y-auto"
        >
            <div
                class="relative"
                x-show="$store.notifications.notifications.length && $store.notifications.hasUnread()"
            >
                <h4 class="mb-0 flex items-center justify-between gap-2 border-b px-5 py-4 text-lg">
                    {{ trans('Notifications') }}
                    <x-button
                        class="rounded-lg bg-heading-foreground/[3%] px-3 py-1 text-2xs"
                        variant="ghost-shadow"
                        @click.prevent="$store.notifications.markAllAsRead()"
                    >
                        {{ trans('Mark All As Read') }}
                    </x-button>
                </h4>
                <ul class="notifications-list">
                    <template x-for="notification in $store.notifications.notifications.filter(notif => notif.unread)">
                        <li
                            class="header-notification-item group/item relative border-b px-5 py-4 transition-all last:border-b-0 hover:bg-heading-foreground/5"
                            :class="{ 'is-read': !notification.unread }"
                        >

                            <h5 class="relative mb-1">
                                <span x-text="notification.title"></span>
                                <span
                                    class="notifications-ping-wrap ms-2 inline-block size-2 rounded-full bg-red-500 align-super group-[&.is-read]/item:hidden"
                                    title="{{ __('Unread Notification') }}"
                                ></span>
                            </h5>
                            <p
                                class="mb-0 w-full overflow-hidden overflow-ellipsis whitespace-nowrap text-2xs opacity-55"
                                x-text="notification.message"
                            ></p>
                            <a
                                class="absolute inset-0 z-0 block"
                                href="#"
                                @click.prevent="$store.notifications.markThenHref(notification)"
                            ></a>
                        </li>
                    </template>
                </ul>
                <div
                    class="absolute inset-0 place-content-center bg-dropdown-background/10 backdrop-blur-sm"
                    :class="{ 'hidden': !$store.notifications.loading, 'grid': $store.notifications.loading }"
                >
                    <x-tabler-loader-2 class="size-9 animate-spin" />
                </div>
            </div>
            <h4
                class="px-4 py-10 text-center last:mb-0"
                x-show="!$store.notifications.notifications.length || !$store.notifications.hasUnread()"
            >
                {{ trans("There's No Notifications") }}
            </h4>
        </x-slot:dropdown>
    </x-dropdown.dropdown>
</div>
