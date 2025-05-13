@extends('panel.layout.app')
@section('title', __('ChatBot'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a
                        class="page-pretitle flex items-center"
                        href="{{  (route('dashboard.index')) }}"
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
                    <h2 class="page-title mb-3">
                        {{ __('ChatBot') }}
                    </h2>
                    <p class="mb-2">{{ __('Manage ChatBot') }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="mb-2">
                @if (1 == 2)
                    @if ($app_is_demo)
                        <a
                            class="btn btn-success"
                            onclick="return toastr.info('This feature is disabled in Demo version.')"
                        >
                            <svg
                                class="mr-2"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none"
                                ></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                <path d="M12 11l0 6"></path>
                                <path d="M9 14l6 0"></path>
                            </svg>
                            {{ __('Add New Template') }}
                        </a>
                    @else
                        <a
                            class="btn btn-success"
                            href="{{  (route('dashboard.page.addOrUpdate')) }}"
                        >
                            <svg
                                class="mr-2"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none"
                                ></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                <path d="M12 11l0 6"></path>
                                <path d="M9 14l6 0"></path>
                            </svg>
                            {{ __('Add New Template') }}
                        </a>
                    @endif
                @endif
            </div>
            <div class="row">
                <div class="col-md-4 mb-8">
                    <form
                        id="chatbot_settings"
                        onsubmit="return chatbotSettingsSave();"
                        enctype="multipart/form-data"
                    >
                        <h3 class="mb-[25px] text-[20px]">{{ __('Settings') }}</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label
                                        class="form-label"
                                        for="status"
                                    >{{ __('Show ChatBot Ballon on') }}</label>
                                    <select
                                        class="form-select"
                                        id="status"
                                        name="status"
                                    >
                                        <option
                                            value="disabled"
                                            {{ $settings_two->chatbot_status == 'disabled' ? 'selected' : null }}
                                        >
                                            {{ __('Disabled') }}</option>
                                        <option
                                            value="frontend"
                                            {{ $settings_two->chatbot_status == 'frontend' ? 'selected' : null }}
                                        >
                                            {{ __('Frontend') }}</option>
                                        <option
                                            value="dashboard"
                                            {{ $settings_two->chatbot_status == 'dashboard' ? 'selected' : null }}
                                        >
                                            {{ __('Dashboard') }}</option>
                                        <option
                                            value="both"
                                            {{ $settings_two->chatbot_status == 'both' ? 'selected' : null }}
                                        >
                                            {{ __('Both') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label
                                        class="form-label"
                                        for="template"
                                    >{{ __('Template') }}</label>
                                    <select
                                        class="form-select"
                                        id="template"
                                        name="template"
                                    >
                                        @foreach ($chatbotData as $chatbot)
                                            <option
                                                value="{{ $chatbot->id }}"
                                                {{ $settings_two->chatbot_template == $chatbot->id ? 'selected' : null }}
                                            >
                                                {{ __($chatbot->title) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Position') }}</label>
                                    <div class="form-selectgroup">
                                        <label class="form-selectgroup-item">
                                            <input
                                                class="form-selectgroup-input"
                                                type="radio"
                                                name="position"
                                                value="top-left"
                                                {{ $settings_two->chatbot_position == 'top-left' ? 'checked' : null }}
                                            />
                                            <span class="form-selectgroup-label">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
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
                                                    <path
                                                        d="M10 3h-5a2 2 0 0 0 -2 2v5a2 2 0 0 0 2 2h5a2 2 0 0 0 2 -2v-5a2 2 0 0 0 -2 -2z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M15 3a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 3a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 8a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 14a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 14a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 19a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M15 19a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M9 19a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 19a1 1 0 0 1 .117 1.993l-.127 .007a1 1 0 0 1 -.117 -1.993l.127 -.007z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                </svg>
                                                {{-- {{__('Top Left')}} --}}
                                            </span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input
                                                class="form-selectgroup-input"
                                                type="radio"
                                                name="position"
                                                value="top-right"
                                                {{ $settings_two->chatbot_position == 'top-right' ? 'checked' : null }}
                                            />
                                            <span class="form-selectgroup-label">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
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
                                                    <path
                                                        d="M19 3.01h-5a2 2 0 0 0 -2 2v5a2 2 0 0 0 2 2h5a2 2 0 0 0 2 -2v-5a2 2 0 0 0 -2 -2z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 14a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 19a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M15 19a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M9 19a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M9 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 19a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 14a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 8a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                </svg>
                                                {{-- {{__('Top Right')}} --}}
                                            </span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input
                                                class="form-selectgroup-input"
                                                type="radio"
                                                name="position"
                                                value="bottom-right"
                                                {{ $settings_two->chatbot_position == 'bottom-right' ? 'checked' : null }}
                                            />
                                            <span class="form-selectgroup-label">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
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
                                                    <path
                                                        d="M19 12h-5a2 2 0 0 0 -2 2v5a2 2 0 0 0 2 2h5a2 2 0 0 0 2 -2v-5a2 2 0 0 0 -2 -2z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 8a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M15 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M9 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M9 19a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 8a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 14a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 19a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                </svg>
                                                {{-- {{__('Bottom Right')}} --}}
                                            </span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input
                                                class="form-selectgroup-input"
                                                type="radio"
                                                name="position"
                                                value="bottom-left"
                                                {{ $settings_two->chatbot_position == 'bottom-left' ? 'checked' : null }}
                                            />
                                            <span class="form-selectgroup-label">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
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
                                                    <path
                                                        d="M10 12h-5a2 2 0 0 0 -2 2v5a2 2 0 0 0 2 2h5a2 2 0 0 0 2 -2v-5a2 2 0 0 0 -2 -2z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 8a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M4 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M9 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M15 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M15 19a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 3a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 8a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 14a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                    <path
                                                        d="M20 19a1 1 0 0 1 .993 .883l.007 .127a1 1 0 0 1 -1.993 .117l-.007 -.127a1 1 0 0 1 1 -1z"
                                                        stroke-width="0"
                                                        fill="currentColor"
                                                    />
                                                </svg>
                                                {{-- {{__('Bottom Left')}} --}}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label
                                        class="form-label"
                                        for="rate_limit"
                                    >{{ __('Message Limit per day') }}</label>
                                    <input
                                        class="form-control"
                                        id="rate_limit"
                                        type="number"
                                        name="rate_limit"
                                        min="2"
                                        max="50"
                                        default="2"
                                        value="{{ $settings_two->chatbot_rate_limit }}"
                                    >
                                </div>
                                <div class="mb-3">
                                    <label
                                        class="form-check form-switch"
                                        for="logged_in"
                                    >
                                        <input
                                            class="form-check-input"
                                            id="logged_in"
                                            type="checkbox"
                                            {{ $settings_two->chatbot_login_require == true ? 'checked' : '' }}
                                        >
                                        <span class="form-check-label">{{ __('Disable if user not logged in?') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button
                            class="btn btn-primary w-full"
                            id="settings_button"
                            form="chatbot_settings"
                        >
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>

                <div class="col-md-7 md:ml-auto">
                    <div class="flex items-baseline justify-between">
                        <h3 class="mb-[25px] text-[20px]">{{ __('Templates') }}</h3>
                        <div class="mb-2">
                            @if ($app_is_demo)
                                <a
                                    class="btn btn-success"
                                    onclick="return toastr.info('This feature is disabled in Demo version.')"
                                >
                                    <svg
                                        class="mr-2"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            stroke="none"
                                            d="M0 0h24v24H0z"
                                            fill="none"
                                        ></path>
                                        <path d="M12 5l0 14"></path>
                                        <path d="M5 12l14 0"></path>
                                    </svg>
                                    {{ __('Add Template') }}
                                </a>
                            @else
                                <a
                                    class="btn btn-success"
                                    href="{{  (route('dashboard.chatbot.addOrUpdate')) }}"
                                >
                                    <svg
                                        class="mr-2"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            stroke="none"
                                            d="M0 0h24v24H0z"
                                            fill="none"
                                        ></path>
                                        <path d="M12 5l0 14"></path>
                                        <path d="M5 12l14 0"></path>
                                    </svg>
                                    {{ __('Add Template') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div
                            class="card-table table-responsive"
                            id="table-default"
                        >
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Role') }}</th>
                                        <th>{{ __('Model') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody text-heading align-middle">
                                    @foreach ($chatbotData as $chatbot)
                                        <tr>
                                            <td class="sort-name w-14"><img
                                                    class="rounded-full"
                                                    src="{{ $chatbot->image ?? custom_theme_url('/assets/img/chat-default.jpg') }}"
                                                    width="32"
                                                    height="32"
                                                /></td>
                                            <td class="sort-name">{{ $chatbot->title }}</td>
                                            <td class="sort-name">{{ $chatbot->role }}</td>
                                            <td class="sort-name">{{ $chatbot->model }}</td>
                                            <td class="w-24 whitespace-nowrap">
                                                @if ($app_is_demo)
                                                    <a
                                                        class="btn h-[36px] w-[36px] border p-0 hover:bg-[var(--tblr-primary)] hover:text-white"
                                                        onclick="return toastr.info('This feature is disabled in Demo version.')"
                                                        title="{{ __('Edit') }}"
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
                                                            />
                                                        </svg>
                                                    </a>
                                                    <a
                                                        class="btn h-[36px] w-[36px] border p-0 hover:bg-red-600 hover:text-white"
                                                        onclick="return toastr.info('This feature is disabled in Demo version.')"
                                                        title="{{ __('Delete') }}"
                                                    >
                                                        <svg
                                                            width="10"
                                                            height="10"
                                                            viewBox="0 0 10 10"
                                                            fill="currentColor"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"
                                                            />
                                                        </svg>
                                                    </a>
                                                @else
                                                    <a
                                                        class="btn h-[36px] w-[36px] border p-0 hover:bg-[var(--tblr-primary)] hover:text-white"
                                                        href="{{  (route('dashboard.chatbot.addOrUpdate', $chatbot->id)) }}"
                                                        title="{{ __('Edit') }}"
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
                                                            />
                                                        </svg>
                                                    </a>
                                                    @if ($chatbot->id != 1)
                                                        <a
                                                            class="btn h-[36px] w-[36px] border p-0 hover:bg-red-600 hover:text-white"
                                                            href="{{  (route('dashboard.chatbot.delete', $chatbot->id)) }}"
                                                            onclick="return confirm('{{ __('Are you sure? This is permanent.') }}')"
                                                            title="{{ __('Delete') }}"
                                                        >
                                                            <svg
                                                                width="10"
                                                                height="10"
                                                                viewBox="0 0 10 10"
                                                                fill="currentColor"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                            >
                                                                <path
                                                                    d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"
                                                                />
                                                            </svg>
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script src="{{ custom_theme_url('/assets/js/panel/openai.js') }}"></script>
        <script src="{{ custom_theme_url('/assets/js/panel/chatbotSettings.js') }}"></script>
    @endpush
