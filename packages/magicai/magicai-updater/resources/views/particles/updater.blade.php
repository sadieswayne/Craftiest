<div class="mx-auto">
    <x-card
        class="text-center shadow-xl shadow-black/5 mb-2"
        variant="shadow"
    >
        <ul>
            <x-updater-li :item="['label' => 'PHP ZIP extension', 'permission' => extension_loaded('zip')]"/>
        </ul>

        @if(! extension_loaded('zip'))
            <x-alert variant="danger" class="mt-2 mb-4">
                <p>
                    @lang("The PHP ZIP extension is not enabled on your server. You need to enable it to use the updater.")
                </p>
            </x-alert>
        @endif

        <x-alert class="mt-2 mb-4">
            <p>
                @lang("To update your system, you first need to download the update checker. This checker is necessary to analyze the system requirements and minimize errors. You can download it by clicking the button below.")
            </p>
        </x-alert>
    </x-card>
    <form
        class=""
        action="{{ route('updater.update') }}"
        method="POST"
    >
        @csrf
        <x-updater-button
            :permission="extension_loaded('zip')"
            :text="extension_loaded('zip') ? __('Download ') . $data['updater_version'].'\'version' : __('Download ') . $data['updater_version'].'\'version'"
        />
    </form>
</div>
