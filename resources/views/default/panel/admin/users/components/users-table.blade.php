<x-table>
    <x-slot:head>
        <tr>
            <th>
                <button
                    class="table-sort"
                    data-sort="sort-name"
                >
                    {{ __('Name') }}
                </button>
            </th>
            <th>
                <button
                    class="table-sort"
                    data-sort="sort-group"
                >
                    {{ __('Role') }}
                </button>
            </th>
            <th>
                <button
                    class="table-sort"
                    data-sort="sort-remaining-words"
                >
                    {{ __('Words Left') }}
                </button>
            </th>
            <th>
                <button
                    class="table-sort"
                    data-sort="sort-remaining-images"
                >
                    {{ __('Images Left') }}
                </button>
            </th>
            <th>
                <button
                    class="table-sort"
                    data-sort="sort-country"
                >
                    {{ __('Country') }}
                </button>
            </th>
            <th>
                <button
                    class="table-sort"
                    data-sort="sort-status"
                >
                    {{ __('Status') }}
                </button>
            </th>
            <th>
                <button
                    class="table-sort"
                    data-sort="sort-date"
                >
                    {{ __('Created At') }}
                </button>
            </th>
            <th class="text-end">
                {{ __('Actions') }}
            </th>
        </tr>
    </x-slot:head>

    <x-slot:body
        class="text-xs"
        id="users-list"
    >
        @if ($app_is_not_demo)
            @forelse ($users as $user)
                <tr>
                    <td class="sort-name">
                        {{ $user?->fullName() }}
                    </td>
                    <td class="sort-group">
                        {{ $user->type->label() }}
                    </td>
                    @php
                        $userWordModel = \App\Domains\Entity\EntityStats::word()->forUser($user);
                        $userImageModel = \App\Domains\Entity\EntityStats::image()->forUser($user);
                    @endphp
                    <td class="sort-remaining-words">

                        {{ $userWordModel->checkIfThereUnlimitedForGroup() ? __('Unlimited') : $userWordModel->totalCreditsForGroup() }}
                    </td>
                    <td class="sort-remaining-images">
                        {{ $userImageModel->checkIfThereUnlimitedForGroup() ? __('Unlimited') : $userImageModel->totalCreditsForGroup() }}
                    </td>
                    <td class="sort-country">
                        {{ $user->country ?? __('Unknown') }}
                    </td>
                    <td class="sort-status">
                        {{ $user->status == 1 ? __('Active') : __('Passive') }}
                    </td>
                    <td
                        class="sort-date"
                        data-date="{{ strtotime($user->created_at) }}"
                    >
                        <p class="m-0">
                            {{ date('j.n.Y', strtotime($user->created_at)) }}
                        </p>
                        <p class="opacity-50">
                            {{ date('H:i:s', strtotime($user->created_at)) }}
                        </p>
                    </td>
                    <td class="whitespace-nowrap text-end">
                        <x-button
                            class="size-9"
                            href="{{  (route('dashboard.user.orders.list', $user)) }}"
                            size="none"
                            variant="ghost-shadow"
                            title="{{ __('Invoice') }}"
                        >
                            <x-tabler-news class="size-4" />
                        </x-button>
                        <x-button
                            class="size-9"
                            hover-variant="primary"
                            size="none"
                            variant="ghost-shadow"
                            href="{{ route('dashboard.admin.users.finance', $user->id) }}"
                            title="{{ __('Finance Management') }}"
                        >
                            <x-tabler-currency-dollar class="size-4" />
                        </x-button>
                        <x-button
                            class="size-9"
                            hover-variant="primary"
                            size="none"
                            variant="ghost-shadow"
                            href="{{ route('dashboard.admin.users.edit', $user->id) }}"
                            title="{{ __('Edit') }}"
                        >
                            <x-tabler-pencil-minus class="size-4" />
                        </x-button>
                        <x-button
                            class="size-9"
                            hover-variant="danger"
                            size="none"
                            variant="ghost-shadow"
                            href="{{ route('dashboard.admin.users.delete', $user->id) }}"
                            onclick="return confirm('{{ __('Are you sure? This is permanent and will delete all documents related to user.') }}')"
                            title="{{ __('Delete') }}"
                        >
                            <x-tabler-x class="size-4" />
                        </x-button>

                        @includeFirst(['affilate::affiliate-setting', 'panel.admin.settings.particles.affiliate-setting', 'vendor.empty'], ['td_wrap' => false])
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        class="text-center"
                        colspan="8"
                    >
                        {{ __('No users found.') }}
                    </td>
                </tr>
            @endforelse
        @else
            <tr>
                <td colspan="8">
                    {{ __('User Informations are hidden in demo due to GDPR. See') }}
                    <a
                        href="https://en.wikipedia.org/wiki/General_Data_Protection_Regulation"
                        target="_blank"
                    >
                        {{ __('What is GDPR') }}
                    </a>
                </td>
            </tr>
            <tr>
                <td class="sort-name">
                    John Doe
                </td>
                <td class="sort-group">
                    User
                </td>
                <td class="sort-remaining-words">
                    12.154
                </td>
                <td class="sort-remaining-images">
                    940
                </td>
                <td class="sort-country">
                    Hungary
                </td>
                <td class="sort-status">
                    Active
                </td>
                <td
                    class="sort-date"
                    data-date="19-12-2022"
                >
                    <p class="m-0">
                        19-12-2022
                    </p>
                    <p class="opacity-50">
                        19-12-2022
                    </p>
                </td>
                <td class="whitespace-nowrap text-end">
                    <x-button
                        class="size-9"
                        hover-variant="primary"
                        size="none"
                        variant="ghost-shadow"
                        onclick="return toastr.info('You cannot edit or remove user in demo mode.')"
                        title="{{ __('Finance Management') }}"
                    >
                        <x-tabler-currency-dollar class="size-4" />
                    </x-button>
                    <x-button
                        class="size-9"
                        hover-variant="primary"
                        size="none"
                        variant="ghost-shadow"
                        onclick="return toastr.info('You cannot edit or remove user in demo mode.')"
                        title="{{ __('Edit') }}"
                    >
                        <x-tabler-pencil-minus class="size-4" />
                    </x-button>
                    <x-button
                        class="size-9"
                        hover-variant="danger"
                        size="none"
                        variant="ghost-shadow"
                        onclick="return toastr.info('You cannot edit or remove user in demo mode.')"
                        title="{{ __('Delete') }}"
                    >
                        <x-tabler-x class="size-4" />
                    </x-button>
                </td>
            </tr>
            <tr>
                <td class="sort-name">
                    Patricia Foe
                </td>
                <td class="sort-group">
                    User
                </td>
                <td class="sort-remaining-words">
                    10.154
                </td>
                <td class="sort-remaining-images">
                    120
                </td>
                <td class="sort-country">
                    Albania
                </td>
                <td class="sort-status">
                    Active
                </td>
                <td
                    class="sort-date"
                    data-date="19-12-2022"
                >
                    <p class="m-0">
                        12-12-2022
                    </p>
                    <p class="opacity-50">
                        19-12-2022
                    </p>
                </td>
                <td class="whitespace-nowrap text-end">
                    <x-button
                        class="size-9"
                        hover-variant="primary"
                        size="none"
                        variant="ghost-shadow"
                        onclick="return toastr.info('You cannot edit or remove user in demo mode.')"
                        title="{{ __('Finance Management') }}"
                    >
                        <x-tabler-currency-dollar class="size-4" />
                    </x-button>
                    <x-button
                        class="size-9"
                        hover-variant="primary"
                        size="none"
                        variant="ghost-shadow"
                        onclick="return toastr.info('You cannot edit or remove user in demo mode.')"
                        title="{{ __('Edit') }}"
                    >
                        <x-tabler-pencil-minus class="size-4" />
                    </x-button>
                    <x-button
                        class="size-9"
                        hover-variant="danger"
                        size="none"
                        variant="ghost-shadow"
                        onclick="return toastr.info('You cannot edit or remove user in demo mode.')"
                        title="{{ __('Delete') }}"
                    >
                        <x-tabler-x class="size-4" />
                    </x-button>
                </td>
            </tr>
        @endif
    </x-slot:body>
</x-table>
