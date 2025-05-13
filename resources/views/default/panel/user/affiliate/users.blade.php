@extends('panel.layout.app')

@section('title', __('Affiliated Users'))
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        <x-card>
            <x-slot:head>
                <h2 class="mb-4">
                    {{ __('Users') }}
                </h2>
                <form
                    class="flex flex-col gap-4"
                    onsubmit="return false;"
                >
                    <x-forms.input
                        class="rounded-full bg-foreground/10 ps-10 placeholder:text-foreground"
                        id="searchInput"
                        type="search"
                        oninput="filterAffiliates()"
                        placeholder="{{ __('Search for username') }}"
                        size="sm"
                    >
                        <x-slot:icon>
                            <span class="absolute start-3 top-1/2 -translate-y-1/2">
                                <x-tabler-search class="size-5" />
                            </span>
                        </x-slot:icon>
                    </x-forms.input>

                    <div class="flex flex-wrap gap-3">
                        <x-forms.input
                            class:container="grow"
                            id="startDate"
                            type="date"
                            label="{{ __('Start Date') }}"
                            type="date"
                            name="startDate"
                            onchange="filterAffiliates()"
                            size="lg"
                        />

                        <x-forms.input
                            class:container="grow"
                            id="endDate"
                            type="date"
                            label="{{ __('End Date') }}"
                            type="date"
                            name="endDate"
                            onchange="filterAffiliates()"
                            size="lg"
                        />
                    </div>
                </form>
            </x-slot:head>

            <x-table
                id="userTable"
                variant="plain"
            >
                <x-slot:head>
                    <tr>
                        <th>
                            {{ __('Full Name') }}
                        </th>
                        <th>
                            {{ __('Amount') }}
                        </th>
                        <th>
                            {{ __('Status') }}
                        </th>
                        <th>
                            {{ __('Date') }}
                        </th>
                    </tr>
                </x-slot:head>

                <x-slot:body>
                    @forelse ($list as $entry)
                        <tr>
                            <td class="sort-id">
                                {{ $entry->name . ' ' . $entry->surname }}
                            </td>
                            <td>
                                {{ $entry->amount }}
                            </td>
                            <td>
                                {{ $entry->status }}
                            </td>
                            <td>
                                {{ $entry->created_at }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                class="text-center"
                                colspan="4"
                            >
                                {{ __('You have no affiliate users') }}
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-table>

            <div
                class="mt-5 flex items-center justify-end"
                id="paginationLinks"
            >
                {{ $list->links() }}
            </div>
        </x-card>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/affiliate.js') }}"></script>
@endpush
