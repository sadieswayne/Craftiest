<div class="w1/3 group cursor-pointer pb-[16px] pt-9 text-center text-[15px] font-medium">
    <figure
        class="size-11 mx-auto mb-4 overflow-hidden rounded-full transition-all group-[&.is-nav-selected]:-translate-y-4 group-[&.is-nav-selected]:scale-[1.75] group-[&.is-nav-selected]:border-[5px] group-[&.is-nav-selected]:border-white group-[&.is-nav-selected]:shadow-sm max-sm:group-[&.is-nav-selected]:scale-150"
    >
        <img
            class="h-full w-full object-cover object-center"
            src="{{ url('') . isset($item->avatar) ? (str_starts_with($item->avatar, 'asset') ? custom_theme_url($item->avatar) : '/testimonialAvatar/' . $item->avatar) : custom_theme_url('assets/img/auth/default-avatar.png') }}"
            alt="{{ __($item->full_name) }}"
        >
    </figure>
    <div class="whitespace-nowrap opacity-0 transition-all group-[&.is-nav-selected]:opacity-100">
        <p class="text-heading-foreground">{!! __($item->full_name) !!}</p>
        <p class="text-heading-foreground opacity-15">{!! __($item->job_title) !!}</p>
    </div>
</div>
