@php
    $user_avatar = Auth::user()->avatar;

    if (!Auth::user()->github_token && !Auth::user()->google_token && !Auth::user()->facebook_token) {
        $user_avatar = '/' . $user_avatar;
    }
@endphp

<x-dropdown.dropdown
    {{ $attributes->twMerge('header-user-dropdown') }}
    anchor="end"
    offsetY="20px"
>
    <x-slot:trigger
        class="{{ @twMerge('size-9 p-0', $attributes->get('class:trigger')) }}"
    >
        @if (isset($trigger) && filled($trigger))
            {{ $trigger }}
        @else
            <span
                class="inline-block size-full rounded-full bg-cover"
                style="background-image: url({{ custom_theme_url($user_avatar) }})"
            ></span>
        @endif
    </x-slot:trigger>

    <x-slot:dropdown
        class="min-w-52"
    >
        <div class="px-3 pt-3">
            <p class="m-0 text-foreground">{{ Auth::user()?->fullName() }}</p>
            <p class="text-3xs text-foreground/70">{{ Auth::user()->email }}</p>
        </div>
        <hr>
		@if($app_is_not_demo)
			@include('components.includes.credit-list-for-user')
		@else
			{!! \Illuminate\Support\Facades\Cache::remember('components.includes.credit-list-for-user', 3600 * 36000, function () {
				return view('components.includes.credit-list-for-user')->render();
            }) !!}
		@endif
        <hr>

        <div class="pb-2 text-2xs">
            <a
                class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
                href="{{ route('dashboard.user.2fa.activate') }}"
            >
                {{ __('2-Factor Auth.') }}
            </a>
            <a
                class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
                href="{{ route('dashboard.user.payment.subscription') }}"
            >
                {{ __('Plan') }}
            </a>
            <a
                class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
                href="{{ route('dashboard.user.orders.index') }}"
            >
                {{ __('Orders') }}
            </a>
            <a
                class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
                href="{{ route('dashboard.user.settings.index') }}"
            >
                {{ __('Settings') }}
            </a>
            <form
                class="flex w-full"
                id="logout"
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf
                <button
                    class="flex w-full items-center px-3 py-2 hover:bg-foreground/10"
                    type="submit"
                >
                    {{ __('Logout') }}
                </button>
            </form>
        </div>

    </x-slot:dropdown>
</x-dropdown.dropdown>
