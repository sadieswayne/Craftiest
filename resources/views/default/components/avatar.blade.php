@php
    $base_class = 'lqd-avatar inline-flex items-center justify-center rounded-full w-10 h-10 bg-heading-background/10 text-heading-foreground text-2xl bg-cover bg-center';
    $avatar = $user->avatar;
    $user_has_avatar_img = $avatar != null;

    if (strpos($avatar, 'http') === false || strpos($avatar, 'https') === false) {
        $avatar = '/' . $avatar;
    }
@endphp

<span {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }} @style([
    'background-image: url(' . custom_theme_url($avatar) . ')' => $user_has_avatar_img,
])>
    @if (!$user_has_avatar_img)
        {{ str()->substr($user->name, 0, 1) . str()->substr($user->surname, 0, 1) }}
    @endif
</span>
