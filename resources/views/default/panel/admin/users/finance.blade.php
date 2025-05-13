@extends('panel.layout.settings')
@section('title', __('Finance Management'))
@section('titlebar_actions', '')

@section('settings')
    <div class="mb-10 flex items-center !gap-3 rounded-xl bg-[rgba(157,107,221,0.1)] !p-4 !py-3 text-[17px] font-semibold">
        <span class="inline-flex !h-6 !w-6 items-center justify-center rounded-full bg-[#9D6BDD] text-[15px] font-bold text-white">1</span>
        {{ __('User Information') }}
    </div>
    <div class="mb-3">
        <label class="form-label">
            {{ __('Username') }}
        </label>
        <input
            class="form-control"
            type="text"
            readonly
            disabled
            value="{{ $user?->fullName() }}"
        >
    </div>
    <div class="mb-3">
        <label class="form-label">
            {{ __('Email') }}
        </label>
        <input
            class="form-control"
            type="text"
            readonly
            disabled
            value="{{ $user->email }}"
        >
    </div>
    <div class="mb-3">
        <label class="form-label">
            {{ __('User Since') }}
        </label>
        <input
            class="form-control"
            type="text"
            readonly
            disabled
            value="{{ $user->created_at }}"
        >
    </div>

    <div class="mb-10 mt-4 flex items-center !gap-3 rounded-xl bg-[rgba(157,107,221,0.1)] !p-4 !py-3 text-[17px] font-semibold">
        <span class="inline-flex !h-6 !w-6 items-center justify-center rounded-full bg-[#9D6BDD] text-[15px] font-bold text-white">2</span>
        {{ __('Manage Subscription') }}
    </div>
    <div class="card mb-10">
        <div
            class="card-header flex justify-between pb-0 pt-3"
            style="border-bottom: none;"
        >
            <label class="form-label">
                {{ __('Current Subscription') }}
                <x-info-tooltip text="{{ __('Assign or delete user subscription.') }}" />
            </label>
            @if ($sub !== null)
                <svg
                    class="cursor-pointer"
                    data-bs-toggle="modal"
                    data-delete-id="{{ $sub->id }}"
                    data-bs-target="#cancelSubs"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                >
                    <mask
                        id="mask0_3965_979"
                        style="mask-type:alpha"
                        maskUnits="userSpaceOnUse"
                        x="0"
                        y="0"
                        width="24"
                        height="24"
                    >
                        <rect
                            width="23.06"
                            height="23.06"
                            fill="#D9D9D9"
                        />
                    </mask>
                    <g mask="url(#mask0_3965_979)">
                        <path
                            d="M6.728 12.4907H16.3363V10.5691H6.728V12.4907ZM11.5322 21.1382C10.203 21.1382 8.95393 20.886 7.78491 20.3816C6.6159 19.8771 5.59902 19.1926 4.73427 18.3278C3.86952 17.4631 3.18492 16.4462 2.68048 15.2772C2.17605 14.1081 1.92383 12.8591 1.92383 11.5299C1.92383 10.2008 2.17605 8.95167 2.68048 7.78265C3.18492 6.61364 3.86952 5.59676 4.73427 4.73201C5.59902 3.86726 6.6159 3.18266 7.78491 2.67823C8.95393 2.17379 10.203 1.92157 11.5322 1.92157C12.8613 1.92157 14.1104 2.17379 15.2794 2.67823C16.4484 3.18266 17.4653 3.86726 18.3301 4.73201C19.1948 5.59676 19.8794 6.61364 20.3838 7.78265C20.8883 8.95167 21.1405 10.2008 21.1405 11.5299C21.1405 12.8591 20.8883 14.1081 20.3838 15.2772C19.8794 16.4462 19.1948 17.4631 18.3301 18.3278C17.4653 19.1926 16.4484 19.8771 15.2794 20.3816C14.1104 20.886 12.8613 21.1382 11.5322 21.1382ZM11.5322 19.2166C13.678 19.2166 15.4956 18.4719 16.9849 16.9826C18.4742 15.4933 19.2188 13.6758 19.2188 11.5299C19.2188 9.38404 18.4742 7.56647 16.9849 6.07717C15.4956 4.58788 13.678 3.84324 11.5322 3.84324C9.3863 3.84324 7.56873 4.58788 6.07943 6.07717C4.59014 7.56647 3.84549 9.38404 3.84549 11.5299C3.84549 13.6758 4.59014 15.4933 6.07943 16.9826C7.56873 18.4719 9.3863 19.2166 11.5322 19.2166Z"
                            fill="#CE3A3A"
                        />
                    </g>
                </svg>
            @endif
        </div>
        <div class="card-body">
            <div class="relative flex">
                <input
                    class="form-control"
                    readonly
                    value="{{ $plan->name ?? __('No Active Subscription') }} {{ __('Plan') }}"
                >
                <div
                    class="absolute right-3 top-[8px] cursor-pointer rounded-full border p-1"
                    data-bs-toggle="modal"
                    data-bs-target="#assignSubs"
                >
                    <svg
                        class="icon icon-tabler icon-tabler-pencil"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none"
                        />
                        <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                        <path d="M13.5 6.5l4 4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <form
        method="POST"
        action="{{  (route('dashboard.user.payment.assignTokenByAdmin')) }}"
    >
        @csrf
        <div class="mb-10 mt-4 flex items-center !gap-3 rounded-xl bg-[rgba(157,107,221,0.1)] !p-4 !py-3 text-[17px] font-semibold">
            <span class="inline-flex !h-6 !w-6 items-center justify-center rounded-full bg-[#9D6BDD] text-[15px] font-bold text-white">3</span>
            {{ __('Token Pack') }}
        </div>
        <div class="card mb-10">
            <div
                class="card-header flex justify-between pb-0 pt-3"
                style="border-bottom: none;"
            >
                <label class="form-label">
                    {{ __('Assign Pack') }}
                    <x-info-tooltip text="{{ __('Assign token pack.') }}" />
                </label>
            </div>
            <div class="card-body">
                <input
                    hidden
                    name="userID"
                    value="{{ $user->id }}"
                >
                <select
                    class="form-select input_type"
                    id="token"
                    name="token"
                    required
                >
                    <option value=""></option>
                    @foreach (getTokenPlans() ?? [] as $plan)
                        <option value="{{ $plan->id }}">
                            <b>{{ __($plan->name) . ' ' . __('Plan') }}</b>
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <style>
            .select-wrapper {
                position: relative;
            }

            .arrow-down {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                width: 0;
                height: 0;
                border-left: 5px solid transparent;
                border-right: 5px solid transparent;
                border-top: 5px solid #ccc;
                /* Change the color as needed */
            }
        </style>
        @if ($app_is_demo)
            <a
                class="btn btn-primary w-full !py-3"
                onclick="return toastr.info('This feature is disabled in Demo version.')"
            >{{ __('Save') }}</a>
        @else
            <button
                class="btn btn-primary w-full !py-3"
                type="submit"
            >
                {{ __('Save') }}
            </button>
        @endif
    </form>

    <!-- =======Cancel Subscription Modal======= -->
    <div
        class="modal fade"
        id="cancelSubs"
        aria-hidden="true"
        tabindex="-1"
    >
        <div
            class="modal-dialog modal-fullscreen-sm-down"
            role="document"
        >
            <div class="modal-content">
                <form
                    id="deleteSubscriptionForm"
                    method="POST"
                    action="{{  (route('dashboard.user.payment.cancelActiveSubscriptionByAdmin', ['id' => $user->id])) }}"
                >
                    @csrf
                    <div class="modal-body">
                        <h5 class="modal-title">{{ __('Are you sure you want to cancel the plan?') }}</h5>
                    </div>
                    <div class="modal-footer">
                        <button
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            type="button"
                        >{{ __('Close') }}</button>
                        <button
                            class="btn btn-danger"
                            type="submit"
                        >{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- =======Assign Subscription Modal======= -->
    <div
        class="modal fade"
        id="assignSubs"
        aria-hidden="true"
        tabindex="-1"
    >
        <div
            class="modal-dialog modal-fullscreen-sm-down"
            role="document"
        >
            <div class="modal-content">
                <form
                    method="POST"
                    action="{{  (route('dashboard.user.payment.assignPlanByAdmin')) }}"
                >
                    @csrf
                    <div class="modal-body">

                        <label class="form-label">
                            {{ __('Select Subscription Plan') }}
                        </label>
                        <input
                            hidden
                            name="userID"
                            value="{{ $user->id }}"
                        >
                        <select
                            class="form-select input_type"
                            name="planID"
                        >
                            <option value=""></option>
                            @foreach (getSubsPlans() ?? [] as $plan)
                                <option value="{{ $plan->id }}">
                                    <b>{{ __($plan->name) . ' ' . __('Plan') }}</b>
                                </option>
                            @endforeach
                        </select>

                        <div
                            id="cron-alert"
                            role="alert"
                        >
                            <x-alert class="mt-2">
                                <p>
                                    {{ __('Please note: Only Free and Lifetime plans are currently available.') }}
                                </p>
                            </x-alert>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            type="button"
                        >{{ __('Close') }}</button>
                        <button
                            class="btn btn-danger"
                            type="submit"
                        >{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Get the modal element
            var modal = new bootstrap.Modal(document.getElementById('cancelSubs'));
            // Add event listener to the modal opening event
            modal._element.addEventListener('show.bs.modal', function(event) {
                // Get the delete-id attribute from the SVG element
                var deleteId = $(event.relatedTarget).data('delete-id');
                // Set the delete-id value in the modal form or content
                $('#deleteSubscriptionForm').append('<input type="hidden" name="deleteId" value="' +
                    deleteId + '">');
                // You can customize this based on your modal structure

            });
        });
    </script>
@endpush
