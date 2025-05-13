@extends('panel.layout.settings')
@section('title', __('Site Logs'))
@section('titlebar_actions', '')

@section('settings')
    @if (Auth::user()->isAdmin())
        @if ($app_is_not_demo)
            <div class="">
                <label
                    class="!mb-5 block"
                    for="log"
                >{{ __('You can copy the below info as simple text with Ctrl+C / Ctrl+V:') }}</label>
                <textarea
                    class="form-control w-full"
                    id="log"
                    name="log"
                    cols="30"
                    rows="20"
                >
			@php
				echo PHP_EOL . '== ' . __('LOGS') . '==' . PHP_EOL;
				$logFile = storage_path('logs/laravel.log');
				if (file_exists($logFile)) {
					$logContent = file_get_contents($logFile);
					echo htmlentities($logContent);
				} else {
					echo __('No logged any data.');
				}
			@endphp 
			</textarea>

                <div class="!mt-5 space-x-2">
                    <x-button
                        id="copyButton"
                        variant="outline"
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
                            <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>
                            <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                        </svg>
                        {{ __('Copy') }}
                    </x-button>
                    <x-button
                        id="clearLogButton"
                        variant="outline"
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
                            <path d="M4 7l16 0"></path>
                            <path d="M10 11l0 6"></path>
                            <path d="M14 11l0 6"></path>
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                        </svg>
                        {{ __('Clear Log File') }}
                    </x-button>
                </div>
            </div>
        @endif

    @endif
@endsection

@push('script')
    <script>
        document.querySelector('textarea').addEventListener('click', function() {
            this.select();
        });

        var clearLogButton = document.getElementById('clearLogButton');

        clearLogButton.addEventListener('click', function() {
            var confirmResult = confirm(@json(__('Are you sure you want to clear the log?')));

            if (confirmResult) {
                // AJAX request to delete the log file
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '/clear-log', true);

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Log file successfully deleted, reload the page
                            location.reload();
                        } else {
                            // Error occurred while deleting the log file
                            alert(@json(__('An error occurred while clearing the log.')));
                        }
                    }
                };

                xhr.send();
            }
        });

        var copyButton = document.getElementById('copyButton');
        var logTextarea = document.getElementById('log');

        copyButton.addEventListener('click', function() {
            logTextarea.select();
            document.execCommand('copy');
            toastr.success(@json(__('Copied')));
        });
    </script>
@endpush
