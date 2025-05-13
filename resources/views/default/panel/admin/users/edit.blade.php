@extends('panel.layout.settings')
@section('title', __('Edit') . ' ' . $user?->fullName())
@section('titlebar_actions', '')

@section('settings')
    <form onsubmit="return userSave({{ $user->id }});">
        <div class="space-y-7">
            <div class="grid grid-cols-2 gap-x-4 gap-y-5">
                <x-forms.input-
                    id="name"
                    type="text"
                    name="name"
                    size="lg"
                    label="{{ __('Name') }}"
                    value="{{ $user->name }}"
                />

                <x-forms.input
                    id="surname"
                    type="text"
                    name="surname"
                    size="lg"
                    label="{{ __('Surname') }}"
                    value="{{ $user->surname }}"
                />

                <x-forms.input
                    id="phone"
                    data-mask="+0000000000000"
                    type="text"
                    name="phone"
                    size="lg"
                    placeholder="+000000000000"
                    label="{{ __('Phone') }}"
                    value="{{ $user->phone }}"
                />

                <x-forms.input
                    id="email"
                    type="email"
                    name="email"
                    size="lg"
                    label="{{ __('Email') }}"
                    value="{{ $user->email }}"
                />

                <x-forms.input
                    id="country"
                    container-class="w-full col-span-2"
                    type="select"
                    name="country"
                    size="lg"
                    label="{{ __('Country') }}"
                >
                    @include('panel.admin.users.countries')
                </x-forms.input>

                <x-forms.input
                    id="type"
                    type="select"
                    name="type"
                    size="lg"
                    label="{{ __('Role') }}"
                >
                    @foreach (App\Enums\Roles::cases() as $role)
                        <option
                            value="{{ $role }}"
                            {{ $user->type === $role ? 'selected' : '' }}
                        >
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </x-forms.input>

                <x-forms.input
                    id="status"
                    type="select"
                    name="status"
                    size="lg"
                    label="{{ __('Status') }}"
                >
                    <option
                        value="1"
                        {{ $user->status == 1 ? 'selected' : '' }}
                    >
                        {{ __('Active') }}
                    </option>
                    <option
                        value="0"
                        {{ $user->status == 0 ? 'selected' : '' }}
                    >
                        {{ __('Passive') }}
                    </option>
                </x-forms.input>
            </div>

            <div x-data="{ showContent: false }">
                <x-button
                    class="flex w-full items-center justify-between gap-7 py-3 text-2xs"
                    type="button"
                    variant="link"
                    @click="showContent = !showContent"
                >
                    <span class="h-px grow bg-current opacity-10"></span>
                    <span class="flex items-center gap-3">
                        {{ __('Credits') }}
                        <x-tabler-chevron-down
                            class="size-4 transition"
                            ::class="{ 'rotate-180': showContent }"
                        />
                    </span>
                    <span class="h-px grow bg-current opacity-10"></span>
                </x-button>
                <div
                    class="hidden pt-5"
                    :class="{ hidden: !showContent }"
                >
                    @livewire('assign-view-credits', ['entities' => $user->entity_credits])
                </div>
            </div>

            <x-button
                class="w-full"
                id="user_edit_button"
                type="submit"
                size="lg"
            >
                {{ __('Save') }}
            </x-button>
        </div>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/user.js') }}"></script>
@endpush
