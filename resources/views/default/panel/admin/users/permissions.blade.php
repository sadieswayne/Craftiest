@extends('panel.layout.settings')
@section('title', __('Manage Admin Permissions'))
@section('titlebar_actions')
    <x-button
            href="{{ route('dashboard.admin.users.index') }}"
    >
        {{ __('User Management') }}
    </x-button>
@endsection
@section('settings')
    <strong class="mt-4">
        @lang('Admin')
    </strong>
    <p class="mb-8">
        @lang('Has control over certain aspects of a system, such as managing users or specific settings, but their permissions are limited to specific areas or functions.')
    </p>
    <strong class="mt-4">
        @lang('Superadmin')
    </strong>
    <p class="mb-8">
        @lang('Has full control over the entire system, including the ability to manage admins and change all settings without restrictions.')
    </p>
    <p class="mb-8">
        @lang('By default, you are assigned as a super admin, giving you the ability to control which areas or functions are accessible to admins.')
    </p>
    <form
            class="flex flex-col gap-5"
            action="{{ route('dashboard.admin.users.permissionSave') }}"
            method="POST"
    >
        @csrf

        <x-form-step
                class="mb-4"
                label="Admin Privileges"
        />
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-2">
            @foreach (\App\Enums\Permissions::cases() as $permission)
                <x-forms.input
                        class:container="h-full bg-input-background"
                        class:label="w-full border h-full rounded px-3 py-4 hover:bg-foreground/5 transition-colors"
                        class="checked-item"
                        id="flex_check_{{ $permission }}"
                        data-filter="{{ $permission }}"
                        :checked="in_array($permission->value, $permissions)"
                        type="checkbox"
                        name="permissionItems[]"
                        value="{{ $permission }}"
                        label="{{ $permission->label() }}"
                        custom
                />
            @endforeach
        </div>

        <x-button
                size="lg"
                type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('[data-filter="check"]').on('change', function () {
                if ($(this).is(':checked')) {
                    $('[data-filter="' + $(this).attr('id') + '"]').prop('checked', true);
                } else {
                    $('[data-filter="' + $(this).attr('id') + '"]').prop('checked', false);
                }
            });
        });
    </script>
@endpush