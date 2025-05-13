<x-table class="lqd-team-members-table">
    <x-slot:head>
        <tr>
            <th>
                @lang('Name')
            </th>
            <th>
                @lang('Status')
            </th>
            <th>
                @lang('Joined')
            </th>
            <th>
                @lang('Role')
            </th>
            <th>
                @lang('Images / Words')
            </th>
            <th class="text-end">
                @lang('Actions')
            </th>
        </tr>
    </x-slot:head>

    <x-slot:body>
        @foreach ($members as $member)
            <tr>
                <td>
                    @if ($member->user_id)
                        <p class="m-0">{{ $member?->user?->name . ' ' . $member?->user?->surname }}</p>
                    @endif
                    <p class="m-0 opacity-60">
                        {{ $member->email }}
                    </p>
                </td>
                <td>
                    <x-badge variant="{{ $member->status == 'waiting' ? 'secondary' : ($member->status == 'active' ? 'success' : 'danger') }}">
                        @lang($member->status)
                    </x-badge>
                </td>
                <td>
                    @if ($member->joined_at)
                        <p class="m-0">
                            {{ $member->joined_at->format('M d, Y') }}
                            <span class="block opacity-60">
                                {{ $member->joined_at->format('H:i:s') }}
                            </span>
                        </p>
                    @endif
                </td>
                <td>
                    {{ $member->role ?: __('unknown') }}
                </td>
                <td>
                    <p class="m-0">
                        @if ($member->remaining_images)
                            {{ $member->remaining_images }}
                        @else
                            {{ $user->remaining_images }}
                        @endif
                        /
                        @if ($member->remaining_words)
                            {{ $member->remaining_words }}
                        @else
                            {{ $user->remaining_words }}
                        @endif
                    </p>
                </td>
                <td class="whitespace-nowrap text-end">
                    <x-button
                        class="size-9"
                        variant="ghost-shadow"
                        size="none"
                        href="{{ route('dashboard.user.team.member.edit', [$team->id, $member->id]) }}"
                        title="{{ __('Edit') }}"
                    >
                        <x-tabler-pencil class="size-4" />
                    </x-button>
                    <x-button
                        class="size-9"
                        variant="ghost-shadow"
                        hover-variant="danger"
                        size="none"
                        href="{{ route('dashboard.user.team.member.delete', [$team->id, $member->id]) }}"
                        onclick="return confirm('Are you sure? This is permanent and will delete all documents related to user.')"
                        title="{{ __('Delete') }}"
                    >
                        <x-tabler-x class="size-4" />
                    </x-button>
                </td>
            </tr>
        @endforeach
    </x-slot:body>
</x-table>
