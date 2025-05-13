@extends('panel.layout.app')
@section('title', __('Advertis'))

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 flex items-center justify-between">
                <div class="col">
                    <a
                        class="page-pretitle flex items-center"
                        href="/dashboard"
                    >
                        <svg
                            class="!me-2 rtl:-scale-x-100"
                            width="8"
                            height="10"
                            viewBox="0 0 6 10"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"
                            />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('Advertis') }}
                    </h2>
                </div>
                <div class="col !text-end">
                    <a
                        class="btn btn-primary"
                        href="{{ route('dashboard.admin.settings.affiliate') }}"
                    >{{ __('Settings') }}</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <h2>{{ __('Advertis') }}</h2>
            <div class="card">
                <div
                    class="card-table table-responsive"
                    id="table-default-2"
                >
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Key') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-tbody align-middle text-heading-foreground">
                            @foreach ($advertises as $advertis)
                                <tr>
                                    <td class="sort-id">{{ $advertis->id }}</td>
                                    <td class="sort-amount">{{ $advertis->key }}</td>
                                    <td class="sort-amount">{{ $advertis->title }}</td>
                                    <td class="sort-status">
                                        @if ($advertis->status)
                                            <span class="rounded-lg bg-green-700/80 p-1 text-center text-white">
                                                Active
                                            </span>
                                        @else
                                            <span class="rounded-lg bg-red-700/80 p-1 text-center text-white">
                                                Passive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a
                                            class="btn h-[36px] w-[36px] border p-0 hover:bg-[var(--tblr-primary)] hover:text-white"
                                            href="{{ route('dashboard.admin.advertis.edit', $advertis->id) }}"
                                        >
                                            <svg
                                                width="13"
                                                height="12"
                                                viewBox="0 0 16 15"
                                                fill="none"
                                                stroke="currentColor"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M9.3125 2.55064L12.8125 5.94302M11.5 12.3038H15M4.5 14L13.6875 5.09498C13.9173 4.87223 14.0996 4.60779 14.224 4.31676C14.3484 4.02572 14.4124 3.71379 14.4124 3.39878C14.4124 3.08377 14.3484 2.77184 14.224 2.48081C14.0996 2.18977 13.9173 1.92533 13.6875 1.70259C13.4577 1.47984 13.1849 1.30315 12.8846 1.1826C12.5843 1.06205 12.2625 1 11.9375 1C11.6125 1 11.2907 1.06205 10.9904 1.1826C10.6901 1.30315 10.4173 1.47984 10.1875 1.70259L1 10.6076V14H4.5Z"
                                                    stroke-width="1.25"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                ></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
