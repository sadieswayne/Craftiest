@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Manage Coupons'))
@section('titlebar_actions')
    <x-modal
        title="{{ __('Add New Coupon') }}"
        disable-modal="{{ $app_is_demo }}"
        disable-modal-message="{{ __('This feature is disabled in Demo version.') }}"
    >
        <x-slot:trigger>
            <x-tabler-plus class="size-4" />
            {{ __('Add New Coupon') }}
        </x-slot:trigger>

        <x-slot:modal>
            <form
                class="flex max-w-[500px] flex-wrap justify-between gap-y-5"
                method="POST"
                action="{{ route('dashboard.admin.coupons.add') }}"
            >
                @csrf

                <x-forms.input
                    class:container="w-full"
                    label="{{ __('Name') }}"
                    size="lg"
                    name="name"
                    value="{{ old('name') }}"
                    required
                />

                <x-forms.input
                    class:container="w-full lg:w-[48%]"
                    type="number"
                    name="discount"
                    value="{{ old('discount') }}"
                    required
                    min="0"
                    max="99"
                    step="0.01"
                    size="lg"
                    label="{{ __('Discount') }} (%)"
                />

                <x-forms.input
                    class:container="w-full lg:w-[48%]"
                    type="number"
                    size="lg"
                    label="{{ __('Limit') }}"
                    name="limit"
                    value="{{ old('limit') }}"
                    placeholder="{{ __('Enter -1 for unlimited usage.') }}"
                    min="-1"
                    required
                />

                <div
                    class="flex w-full flex-wrap justify-between"
                    x-data="{
                        codeType: 'auto',
                        changeCodeType(value) {
                            this.codeType = value;
                        }
                    }"
                >
                    <label class="lqd-input-label mb-3 flex w-full cursor-pointer items-center gap-2 text-2xs font-medium leading-none text-label">
                        {{ __('Code') }}
                    </label>
                    <div class="mb-3 flex flex-wrap gap-3">
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="code"
                                value="auto"
                                checked
                                @change="changeCodeType($event.target.value)"
                            />
                            {{ __('Auto Generate') }}
                        </label>
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="code"
                                value="manual"
                                @change="changeCodeType($event.target.value)"
                            />
                            {{ __('Manual Generate') }}
                        </label>
                    </div>

                    <x-forms.input
                        class:container="w-full"
                        class="hidden"
                        ::class="{ 'hidden': codeType === 'auto' }"
                        type="text"
                        name="codeInput"
                        onkeydown="return (event.key !== ' ')"
                        oninput="this.value = this.value.toUpperCase()"
                        maxlength="7"
                        ::required="codeType === 'manual'"
                        size="lg"
                    />
                </div>

                <div class="mt-4 w-full border-t pt-3">
                    <x-button
                        @click.prevent="modalOpen = false"
                        variant="outline"
                        type="button"
                    >
                        {{ __('Cancel') }}
                    </x-button>
                    <x-button type="submit">
                        {{ __('Save changes') }}
                    </x-button>
                </div>
            </form>
        </x-slot:modal>
    </x-modal>
@endsection

@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Name') }}
                    </th>
                    <th>
                        {{ __('Code') }}
                    </th>
                    <th>
                        {{ __('Discount (%)') }}
                    </th>
                    <th>
                        {{ __('Limit') }}
                    </th>
                    <th>
                        {{ __('Used') }}
                    </th>
                    <th>
                        {{ __('Created By') }}
                    </th>
                    <th class="text-end">
                        {{ __('Action') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($list ?? [] as $entry)
                    <tr>
                        <td>
                            {{ $entry->name }}
                        </td>
                        <td>
                            {{ $entry->code }}
                        </td>
                        <td>
                            {{ $entry->discount }}%
                        </td>
                        <td>
                            {{ $entry->limit }}
                        </td>
                        <td>
                            {{ $entry->usersUsed->count() }}
                        </td>
                        <td>
                            <p class="m-0">
                                {{ $entry->createdBy->name }}
                                <span class="block opacity-60">
                                    {{ date('j.n.Y', strtotime($entry->created_at)) }}
                                </span>
                            </p>
                        </td>

                        <td class="whitespace-nowrap text-end">
                            <x-button
                                class="size-9"
                                variant="ghost-shadow"
                                size="none"
                                href="{{  (route('dashboard.admin.coupons.used', $entry->id)) }}"
                                title="{{ __('View') }}"
                            >
                                <x-tabler-eye class="size-4" />
                            </x-button>
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
                                <x-modal
                                    class="inline-flex"
                                    title="{{ __('Edit Coupon') }} - [{{ $entry->code }}]"
                                >
                                    <x-slot:trigger
                                        class="size-9"
                                        size="none"
                                        variant="ghost-shadow"
                                        title="{{ __('Edit Coupon') }}"
                                    >
                                        <x-tabler-pencil class="size-4" />
                                    </x-slot:trigger>

                                    <x-slot:modal>
                                        <form
                                            class="flex flex-wrap justify-between gap-y-5"
                                            action="{{ route('dashboard.admin.coupons.edit', $entry->id) }}"
                                            method="post"
                                        >
                                            @csrf

                                            <x-forms.input
                                                class:container="w-full"
                                                id="Ename"
                                                size="lg"
                                                name="name"
                                                required
                                                label="{{ __('Name') }}"
                                                value="{{ $entry->name }}"
                                            />

                                            <x-forms.input
                                                class:container="w-full md:w-[48%]"
                                                id="Ediscount"
                                                type="number"
                                                name="discount"
                                                required
                                                min="0"
                                                max="99"
                                                step="0.01"
                                                size="lg"
                                                label="{{ __('Discount') }} (%)"
                                                value="{{ $entry->discount }}"
                                            />

                                            <x-forms.input
                                                class:container="w-full md:w-[48%]"
                                                id="Elimit"
                                                type="number"
                                                name="limit"
                                                placeholder="{{ __('Enter -1 for unlimited usage.') }}"
                                                min="-1"
                                                required
                                                label="{{ __('Limit') }}"
                                                size="lg"
                                                value="{{ $entry->limit }}"
                                            />

                                            <div class="mt-4 w-full border-t pt-3">
                                                <x-button
                                                    @click.prevent="modalOpen = false"
                                                    variant="outline"
                                                    type="button"
                                                >
                                                    {{ __('Cancel') }}
                                                </x-button>
                                                <x-button type="submit">
                                                    {{ __('Save changes') }}
                                                </x-button>
                                            </div>
                                        </form>
                                    </x-slot:modal>
                                </x-modal>
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    href="{{  (route('dashboard.admin.coupons.delete', $entry->id)) }}"
                                    onclick="confirm('{{ __('Are you sure? This is permanent.') }}')"
                                    title="{{ __('Delete') }}"
                                >
                                    <x-tabler-x class="size-4" />
                                </x-button>
                            @endif
                        </td>

                    </tr>
                @endforeach
                @if (count($list ?? []) == 0)
                    <tr>
                        <td
                            class="text-center"
                            colspan="7"
                        >
                            {{ __('There is no coupons yet') }}
                        </td>
                    </tr>
                @endif
            </x-slot:body>
        </x-table>
    </div>
@endsection
