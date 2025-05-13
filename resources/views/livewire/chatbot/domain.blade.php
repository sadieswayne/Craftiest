<div>
    <li class="flex items-center justify-between py-2.5 pl-2 pr-4">
        <div class="flex w-0 flex-1 items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-5 w-5 flex-shrink-0 text-gray-400">
                <path d="M21.721 12.752a9.711 9.711 0 00-.945-5.003 12.754 12.754 0 01-4.339 2.708 18.991 18.991 0 01-.214 4.772 17.165 17.165 0 005.498-2.477zM14.634 15.55a17.324 17.324 0 00.332-4.647c-.952.227-1.945.347-2.966.347-1.021 0-2.014-.12-2.966-.347a17.515 17.515 0 00.332 4.647 17.385 17.385 0 005.268 0zM9.772 17.119a18.963 18.963 0 004.456 0A17.182 17.182 0 0112 21.724a17.18 17.18 0 01-2.228-4.605zM7.777 15.23a18.87 18.87 0 01-.214-4.774 12.753 12.753 0 01-4.34-2.708 9.711 9.711 0 00-.944 5.004 17.165 17.165 0 005.498 2.477zM21.356 14.752a9.765 9.765 0 01-7.478 6.817 18.64 18.64 0 001.988-4.718 18.627 18.627 0 005.49-2.098zM2.644 14.752c1.682.971 3.53 1.688 5.49 2.099a18.64 18.64 0 001.988 4.718 9.765 9.765 0 01-7.478-6.816zM13.878 2.43a9.755 9.755 0 016.116 3.986 11.267 11.267 0 01-3.746 2.504 18.63 18.63 0 00-2.37-6.49zM12 2.276a17.152 17.152 0 012.805 7.121c-.897.23-1.837.353-2.805.353-.968 0-1.908-.122-2.805-.353A17.151 17.151 0 0112 2.276zM10.122 2.43a18.629 18.629 0 00-2.37 6.49 11.266 11.266 0 01-3.746-2.504 9.754 9.754 0 016.116-3.985z" />
            </svg>
            <div class="ml-2 w-0 flex-1 space-x-1 truncate">
                <span class="text-gray-900 dark:text-gray-300 text-2xs">{{ $domain->domain }}</span>
            </div>
        </div>
        <div class="ml-4 flex-shrink-0">
            <div class="z-0 flex gap-x-3">
                <span class="h-auto w-px bg-gray-200 dark:bg-gray-700"></span>
                <livewire:chatbot-code :domain="$domain" />

                <span class="h-auto w-px bg-gray-200 dark:bg-gray-700"></span>
                <div x-data="clipboard({ text: '{{ $domain->app_key }}', message: '{{ __('APP Key successfully copied!') }}' })">
                    <button title="Copy App Key" type="button" class="text-2xs dark:text-gray-300" x-bind="clipboard">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-500">
                            <path fill-rule="evenodd" d="M17.663 3.118c.225.015.45.032.673.05C19.876 3.298 21 4.604 21 6.109v9.642a3 3 0 0 1-3 3V16.5c0-5.922-4.576-10.775-10.384-11.217.324-1.132 1.3-2.01 2.548-2.114.224-.019.448-.036.673-.051A3 3 0 0 1 13.5 1.5H15a3 3 0 0 1 2.663 1.618ZM12 4.5A1.5 1.5 0 0 1 13.5 3H15a1.5 1.5 0 0 1 1.5 1.5H12Z" clip-rule="evenodd" />
                            <path d="M3 8.625c0-1.036.84-1.875 1.875-1.875h.375A3.75 3.75 0 0 1 9 10.5v1.875c0 1.036.84 1.875 1.875 1.875h1.875A3.75 3.75 0 0 1 16.5 18v2.625c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625v-12Z" />
                            <path d="M10.5 10.5a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963 5.23 5.23 0 0 0-3.434-1.279h-1.875a.375.375 0 0 1-.375-.375V10.5Z" />
                        </svg>
                    </button>
                </div>

                <span class="h-auto w-px bg-gray-200 dark:bg-gray-700"></span>
                <div>
                    <button type="button" wire:click="showModal" class="text-sm">
                        <svg title="Edit" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-5 w-5 flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-500">
                            <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                            <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                        </svg>
                    </button>
                </div>
                <span class="h-auto w-px bg-gray-200 dark:bg-gray-700"></span>
                <div>
                    <button type="button" wire:click="showDeleteModal" class="text-sm">
                        <svg title="Delete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-5 w-5 flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-500">
                            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        @if ($editDomainDialog)
            <x-modal-new
                    wire:model.live="editDomainDialog"
                    :title="__('Update Domain')"
                    :description="$domain->chatbot->title"
                    focusable
                    inline-buttons
                    max-width="sm"
                    :cancel-attributes="[
                         'wire:click' => 'closeModal',
                         'wire:loading.attr' => 'disabled',
                     ]">

                <div x-data="{}" x-on:confirming-edit-domain.window="setTimeout(() => $refs.domain.focus(), 250)">
                    <input type="text" class="form-control w-full rounded-md border border-gray-200"
                           required
                           placeholder="{{ __('example.com') }}"
                           x-ref="domain"
                           wire:model="editingDomain"
                           wire:keydown.prevent.enter="editDomain" />

                    @error('editingDomain')
                    <p class="text-red-500">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <x-slot:buttons>
                    <div class="justify-self-end">
                        <x-button type="button" class="self-center" variant="info" size="md" wire:click="editDomain" wire:loading.attr="disabled">
                            {{ __('Update') }}
                        </x-button>
                    </div>
                </x-slot:buttons>
            </x-modal-new>
        @endif
        @if ($deleteDomainDialog)
            <x-modal-new
                    wire:model.live="deleteDomainDialog"
                    :title="__('Delete Domain')"
                    :description="$domain->domain"
                    focusable
                    max-width="sm"
                    :cancel-attributes="[
                         'wire:click' => 'closeModal',
                         'wire:loading.attr' => 'disabled',
                     ]">

                <div class="grid gap-4">
                    <span class="text-sm mb-2">{{ __('Are you sure you want to delete this domain?') }}</span>
                    <div class="flex gap-4 w-full">
                        <x-button type="button" class="self-center w-full" variant="outline" size="md" wire:click="closeModal" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </x-button>
                        <x-button type="button" class="self-center w-full" variant="danger" size="md" wire:click="delete" wire:loading.attr="disabled">
                            {{ __('Delete') }}
                        </x-button>
                    </div>
                </div>
            </x-modal-new>
        @endif
    </li>

</div>
