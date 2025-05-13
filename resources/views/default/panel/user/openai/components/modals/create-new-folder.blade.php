<form
    action="{{ route('dashboard.user.openai.documents.new-folder') }}"
    method="post"
>
    @csrf
    <x-forms.input
        label="{{ __('New Folder Name:') }}"
        name="newFolderName"
        required
    />
    <div class="mt-4 border-t pt-3 text-end">
        <x-button
            @click.prevent="modalOpen = false"
            variant="outline"
        >
            {{ __('Cancel') }}
        </x-button>
        <x-button
            tag="button"
            type="submit"
        >
            {{ __('Add') }}
        </x-button>
    </div>
</form>
