<div class="mx-auto mt-6">
   <div class="flex flex-col mb-4 ">
       <div class="flex justify-center">
           <x-tabler-file-zip class="size-16 text-green-600" />

       </div>

         <p class="text-sm font-bold ml-2">
             Great! Your system is backed up successfully.
         </p>
         <small class="mt-2 p-2 border rounded bg-blue-200">
             {{ $fileName }}
         </small>
   </div>

    <form
        id="upgrade-form"
        action="{{ route('updater.upgrade') }}"
        method="POST"
    >
        @csrf
        <x-updater-button
            id="update-button"
            :permission="extension_loaded('zip')"
            :text="$permission ? __('Upgrade ') . $data['version'].'\'version' : __('Upgrade ') . $data['version'].'\'version'"
        />
    </form>
</div>

@push('extra_script')
    <script>

        $('#upgrade-form').on('submit', function(event) {
            event.preventDefault();

            $('#update-button').attr('disabled', true);
            $('#update-button').text('Downloading && Updating...');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                url: "{{ route('updater.upgrade') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'POST',
                },
                // Dosya yüklemesi varsa aşağıdaki ayarları eklemelisiniz
                processData: false,
                contentType: false,
                success: function (response) {

                    if(response.type === 'success')
                    {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }

                    setTimeout(() => {
                        window.location.href = '/updater';
                    }, 1000);
                },
                error: function (xhr, status, error) {
                    setTimeout(() => {
                        recursiveVersionCheck();
                    }, 1000);
                }
            });
        });

        function recursiveVersionCheck() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                url: "{{ route('updater.version-check') }}",
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'POST',
                },
                // Dosya yüklemesi varsa aşağıdaki ayarları eklemelisiniz
                processData: false,
                contentType: false,
                success: function (response) {
                    if(response.updated) {
                        toastr.success('Upgrade completed successfully');

                        setTimeout(() => {
                            window.location.href = '/updater';
                        }, 1000);
                    }else {
                        setTimeout(() => {
                            recursiveVersionCheck();
                        }, 10000);
                    }
                },
                error: function (xhr, status, error) {
                    setTimeout(() => {
                        recursiveVersionCheck();
                    }, 10000);
                }
            })
        }
    </script>
@endpush
