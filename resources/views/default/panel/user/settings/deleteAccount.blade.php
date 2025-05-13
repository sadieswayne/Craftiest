@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Request Account Deletion'))
@section('titlebar_actions', '')

@section('settings')
    <div class="flex flex-col justify-between gap-y-7">
        <x-card
            class="relative w-full pb-10 [&_hr]:my-5 [&_hr]:border-border"
            variant="shadow"
            size="lg"
        >
            <x-tabler-trash class="size-[100px] mb-8 rounded-full bg-[#FF0000]/10 stroke-[#FF0000] p-2" />

            <div class="mb-8 flex flex-wrap items-center gap-2">
                <h3 class="m-0 text-[23px] font-semibold">
                    {{ __('Request Account Deletion') }}
                </h3>
                <p>
                    {{ __('Are you sure you want to delete the account linked to ') . auth()->user()->email . __(' ?') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-6 text-sm font-medium text-heading-foreground">
                <x-forms.input
                    class:container="mb-2"
                    id="understand"
                    type="checkbox"
                    label="{{ __('I understand that I will not be able to recover my account after deletion.') }}"
                    switcher
                    required
                />
                <x-forms.input
                    class:container="mb-2"
                    id="password"
                    type="password"
                    label="{{ __('Password') }}"
                    placeholder="{{ __('Enter your password') }}"
                    required
                />
                <x-button
                    variant="danger"
                    onclick="checkAndConfirm('{{ __('Please be aware that all data and information stored in your account will be permanently deleted and cannot be recovered.') }}')"
                >
                    <x-tabler-circle-minus class="size-4" />
                    {{ __('Request Account Deletion') }}
                </x-button>
            </div>
        </x-card>
    </div>
@endsection

@push('script')
    <script>
        function checkAndConfirm(message) {
            if (!document.getElementById('understand').checked || $('#password').val() === '') {
                toastr.error('{{ __('Please confirm that you understand the consequences of deleting your account and enter your password.') }}');
                return false;
            }
            //  confirm(message) the send ajax post request to deleteAccountSend
            if (confirm(message)) {
                $.ajax({
                    url: '{{ route('dashboard.user.settings.deleteAccount.send') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        password: $('#password').val()
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            }
        }
    </script>
@endpush
