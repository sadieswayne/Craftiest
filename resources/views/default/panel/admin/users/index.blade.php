@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('User Management'))

@push('css')
    <link
        href="{{ custom_theme_url('/assets/libs/datepicker/air-datepicker.css') }}"
        rel="stylesheet"
    />
@endpush

@section('titlebar_actions')
    <div
        class="me-3"
        x-data="{
            datepicker: null,
            init() {
                this.getInvoices = this.getInvoices.bind(this);
        
                this.datepicker = new AirDatepicker('#invoices-datepicker', {
                    inline: true,
                    isMobile: window.innerWidth <= 768,
                    autoClose: window.innerWidth <= 768,
                    range: true,
                    multipleDatesSeparator: ' - ',
                    locale: defaultLocale
                });
            },
            getInvoices() {
                var selectedDates = this.datepicker.selectedDates;
                window.location.href = '{{ route('dashboard.user.orders.invoices.export') }}?start_date=' + selectedDates[0] + '&end_date=' + selectedDates[1];
            }
        }"
    >
        <x-dropdown.dropdown
            class="me-3"
            anchor="end"
            offsetY="20px"
        >
            <x-slot:trigger>
                @lang('Export Invoices')
                <x-tabler-news class="size-5" />
            </x-slot:trigger>

            <x-slot:dropdown
                class="p-3"
            >
                <input
                    class="hidden"
                    id="invoices-datepicker"
                    type="text"
                >

                <div class="mt-3 flex justify-end border-t pt-3">
                    <x-button
                        type="button"
                        @click.prevent="getInvoices"
                    >
                        @lang('Export')
                        <x-tabler-download class="size-4" />
                    </x-button>
                </div>
            </x-slot:dropdown>
        </x-dropdown.dropdown>
    </div>

    <x-dropdown.dropdown
        class="me-3"
        anchor="end"
        offsetY="20px"
    >
        <x-slot:trigger>
            {{ __('Export Users') }}
            <x-tabler-dots-vertical class="size-4" />
        </x-slot:trigger>

        <x-slot:dropdown
            class="overflow-hidden whitespace-nowrap py-1 text-2xs font-medium group-[&[data-view-mode=grid]]:-me-3"
        >
            <x-button
                class="flex w-full justify-start rounded-none px-3 py-2 text-2xs shadow-none hover:translate-y-0 hover:bg-foreground/5 hover:text-inherit hover:shadow-none focus-visible:bg-foreground/5 focus-visible:text-inherit"
                size="none"
                variant="ghost-shadow"
                href="{{ route('dashboard.admin.users.userExport', ['type' => 'pdf']) }}"
            >
                <x-tabler-download class="size-4 text-blue-600" />
                {{ __('PDF') }}
            </x-button>
            <x-button
                class="flex w-full justify-start rounded-none px-3 py-2 text-2xs shadow-none hover:translate-y-0 hover:bg-foreground/5 hover:text-inherit hover:shadow-none focus-visible:bg-foreground/5 focus-visible:text-inherit"
                size="none"
                variant="ghost-shadow"
                href="{{ route('dashboard.admin.users.userExport', ['type' => 'excel']) }}"
            >
                <x-tabler-download class="size-4 text-blue-600" />
                {{ __('Excel') }}
            </x-button>
            <x-button
                class="flex w-full justify-start rounded-none px-3 py-2 text-2xs shadow-none hover:translate-y-0 hover:bg-foreground/5 hover:text-inherit hover:shadow-none focus-visible:bg-foreground/5 focus-visible:text-inherit"
                size="none"
                variant="ghost-shadow"
                href="{{ route('dashboard.admin.users.userExport', ['type' => 'csv']) }}"
            >
                <x-tabler-download class="size-4 text-blue-600" />
                {{ __('CSV') }}
            </x-button>
        </x-slot:dropdown>
    </x-dropdown.dropdown>

    <x-button
        href="{{ $app_is_demo ? '#' : route('dashboard.admin.users.create') }}"
        onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        variant="primary"
    >
        <x-tabler-user-plus class="size-5" />
        {{ __('Add new user') }}
    </x-button>
@endsection
@section('titlebar_after')
    <div class="w-full md:w-1/2 lg:w-1/3">
        <form
            x-init
            x-target="users-list"
            action="/dashboard/admin/users/search"
        >
            <x-forms.input
                class="lqd-users-search-input rounded-full bg-foreground/10 ps-10 placeholder:text-foreground"
                id="search"
                name="search"
                size="sm"
                type="search"
                placeholder="{{ __('Search users') }}"
                @input.debounce="$el.form.requestSubmit()"
                @search="$el.form.requestSubmit()"
            >
                <x-slot:icon>
                    <span class="absolute start-3 top-1/2 -translate-y-1/2">
                        <x-tabler-search class="size-5" />
                    </span>
                </x-slot:icon>
            </x-forms.input>
        </form>
    </div>
@endsection

@section('content')
    <div class="py-10">
        @include('panel.admin.users.components.users-table', ['users' => $users])

        @if ($app_is_not_demo)
            <div class="mt-1 flex items-center justify-end border-t pt-4">
                <div class="m-0 ms-auto p-0">{{ $users->links() }}</div>
            </div>
        @endif
    </div>

@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/datepicker/air-datepicker.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/datepicker/locale/en.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/user.js') }}"></script>
    @includeFirst(['affilate::affiliate-setting-script', 'panel.admin.settings.particles.affiliate-setting-script', 'vendor.empty'])
@endpush
