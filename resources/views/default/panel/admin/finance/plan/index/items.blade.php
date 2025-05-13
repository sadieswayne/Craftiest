@if ($gatewayError == true)
    <div class="mt-2 rounded-xl bg-amber-100 p-4 text-amber-600 dark:bg-amber-600/20 dark:text-amber-200">
        <div class="mb-5 flex flex-wrap items-center gap-2">
            <x-tabler-info-circle class="size-5" />
            {{ __('Gateway is set to use sandbox. Please set mode to development!') }}
        </div>
        <ul class="list-inside list-disc">
            <li>
                {{ __('To use live settings:') }}
                <ul class="list-inside list-disc ps-4">
                    <li>{{ __('Set mode to Production') }}</li>
                    <li>{{ __('Save gateway settings') }}</li>
                    <li>{{ __('Know that all defined products and prices will reset.') }}</li>
                </ul>
            </li>
            <li>
                {{ __('To use sandbox settings:') }}
                <ul class="list-inside list-disc ps-4">
                    <li>{{ __('Set mode to Development') }}</li>
                    <li>{{ __('Save gateway settings') }}</li>
                    <li>{{ __('Know that all defined products and prices will reset.') }}</li>
                </ul>
            </li>
            <li>{{ __('Beware of that order is important. First set mode then save gateway settings.') }}</li>
        </ul>
    </div>
@endif

<x-table>
    <x-slot:head>
        <tr>
            <th>
                {{ __('Status') }}
            </th>
            <th>
                {{ __('Type') }}
            </th>
            <th>
                {{ __('Name') }}
            </th>
            <th>
                {{ __('Price') }}
            </th>
            <th>
                {{ __('Frequency') }}
            </th>
            <th>
                {{ __('Updated At') }}
            </th>
            <th class="text-end">
                {{ __('Actions') }}
            </th>
        </tr>
    </x-slot:head>

    <x-slot:body>
        @foreach ($plans as $entry)
            <tr>
                <td>
                    <x-badge
                            class="text-2xs"
                            variant="{{ $entry->active == 1 ? 'success' : 'danger' }}"
                    >
                        {{ $entry->active == 1 ? __('Active') : __('Passive') }}
                    </x-badge>
                </td>
                <td>
                    {{ $entry->type == 'prepaid' ? __('Token Pack') : __('Subscription') }}
                </td>
                <td>
                    {{ $entry->name }}
                </td>
                <td>
                    {{ $entry->price }}
                </td>
                <td>
                    {{ $entry->type == 'prepaid' ? __('One Time') : __(formatCamelCase($entry->frequency)) }}
                </td>
                <td>
                    <p class="m-0">
                        {{ date('j.n.Y', strtotime($entry->updated_at)) }}
                        <span class="block opacity-60">
                                        {{ date('H:i:s', strtotime($entry->updated_at)) }}
                                    </span>
                    </p>
                </td>
                <td class="whitespace-nowrap text-end">
                    @if ($app_is_demo)
                        <x-button
                                class="size-9"
                                variant="ghost-shadow"
                                size="none"
                                onclick="return toastr.info('This feature is disabled in Demo version.')"
                                title="{{ __('Edit') }}"
                        >
                            <x-tabler-pencil class="size-4" />
                        </x-button>
                        <x-button
                                class="size-9"
                                variant="ghost-shadow"
                                hover-variant="danger"
                                size="none"
                                onclick="return toastr.info('This feature is disabled in Demo version.')"
                                title="{{ __('Delete') }}"
                        >
                            <x-tabler-x class="size-4" />
                        </x-button>
                    @else
                        <x-button
                                class="size-9"
                                variant="ghost-shadow"
                                size="none"
                                href="{{ route($entry->type == 'subscription' ? 'dashboard.admin.finance.plan.edit' : 'dashboard.admin.finance.token-pack-plan.edit', $entry->id) }}"
                                title="{{ __('Edit') }}"
                        >
                            <x-tabler-pencil class="size-4" />
                        </x-button>
                        <x-button
                                class="size-9"
                                variant="ghost-shadow"
                                hover-variant="danger"
                                size="none"
                                onclick="return confirm('Do you want to delete this plan? All subscriptions will be cancelled. This is not reversible.');"
                                href="{{ route('dashboard.admin.finance.plan.destroy', $entry->id) }}"
                                title="{{ __('Delete') }}"
                        >
                            <x-tabler-x class="size-4" />
                        </x-button>
                    @endif
                </td>
            </tr>
        @endforeach

    </x-slot:body>
</x-table>
