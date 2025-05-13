@extends('panel.layout.settings')
@section('title', __('Create New User'))
@section('titlebar_actions', '')

@section('settings')
    <form
        @if ($app_is_demo) @else action="{{ route('dashboard.admin.users.store') }}" @endif
    method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input
                                class="form-control"
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Surname') }}</label>
                            <input
                                class="form-control"
                                id="surname"
                                type="text"
                                name="surname"
                                value="{{ old('surname') }}"
                                required
                            >
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input
                                class="form-control"
                                id="phone"
                                data-mask="+0000000000000"
                                data-mask-visible="true"
                                type="text"
                                name="phone"
                                placeholder="+000000000000"
                                autocomplete="off"
                                value="{{ old('phone') }}"
                            />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input
                                class="form-control"
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                            >
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Avatar') }}</label>
                    <input
                        class="form-control"
                        id="avatar"
                        type="file"
                        name="avatar"
                    >
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Password') }}
                            </label>
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="{{ __('Your password') }}"
                                    value="{{ old('password') }}"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('Re-Password') }}
                            </label>
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    id="repassword"
                                    type="password"
                                    name="repassword"
                                    placeholder="{{ __('Repeat password') }}"
                                    value="{{ old('repassword') }}"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Country') }}</label>
                    <select
                        class="form-select"
                        id="country"
                        type="text"
                        name="country"
                    >
                        @include('panel.admin.users.countries')
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-label">{{ __('Type') }}</div>
                            <select
                                class="form-select"
                                id="type"
                                name="type"
                            >
                                @foreach(\App\Enums\Roles::cases() as $role)
                                    <option value="{{ $role->value }}">
                                        {{ $role->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-label">{{ __('Status') }}</div>
                            <select
                                class="form-select"
                                id="status"
                                name="status"
                            >
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Passive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="accordion " id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button
                                    class="accordion-button form-control"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne"
                                    aria-expanded="true"
                                    aria-controls="collapseOne"
                                >
                                    {{ __('Credits') }}
                                </button>
                            </h2>
                            <div
                                id="collapseOne"
                                class="accordion-collapse collapse show"
                                aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample"
                            >
                                <div class="accordion-body">
                                    @livewire('assign-view-credits', ['entities' => \App\Models\User::getFreshCredits()])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button
                    class="btn btn-primary w-full"
                    @if ($app_is_demo) type="button" @else type="submit" @endif
                >
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>
@endsection
