<form
    id="folder-rename-form-{{ $folder_id }}"
    x-data="{ folderId: '{{ $folder_id }}', folderName: '{{ $folder_name }}' }"
    @submit.prevent="$ajax('/dashboard/user/openai/documents/update-folder/{{ $folder_id }}', {
		method: 'post',
		body: { _token: '{{ csrf_token() }}', newFolderName: folderName },
		events: true,
	})"
    @ajax:missing="$event.preventDefault()"
    @ajax:error="toastr.error('{{ __('Error updating folder name') }}')"
    @ajax:success="
		modalOpen = false;
		document.querySelector('#folder-name-{{ $folder_id }}').innerText = folderName;
		toastr.success('{{ __('Folder name updated successfully') }}');
	"
    action="{{ "/dashboard/user/openai/documents/update-folder/$folder_id" }}"
    method="post"
>
    @csrf
    <input
        type="hidden"
        name="modalFolderId"
        x-model="folderId"
        value="{{ $folder_id }}"
        readonly
    >
    <x-forms.input
        label="{{ __('Folder new name:') }}"
        type="text"
        name="newFolderName"
        x-model="folderName"
        value="{{ $folder_name }}"
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
            {{ __('Save changes') }}
        </x-button>
    </div>
</form>
