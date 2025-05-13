<form class="flex flex-col gap-3 pt-3" id="folder-delete-form-{{ $folder_id }}" x-data="{ folderId: '{{ $folder_id }}', folderName: '{{ $folder_name }}', deleteAll: false }"
    @submit.prevent="$ajax('/dashboard/user/openai/documents/delete-folder/{{ $folder_id }}', {
		method: 'post',
		body: { _token: '{{ csrf_token() }}', all: deleteAll ? 1 : 0 },
		events: true,
	})"
    @ajax:missing="$event.preventDefault()" @ajax:error="toastr.error('{{ __('Error deleting folder') }}')"
    @ajax:success="
		modalOpen = false;
		toastr.success('{{ __('Folder deleted successfully') }}');
		location.reload();
	"
    action="{{ "/dashboard/user/openai/documents/delete-folder/$folder_id" }}" method="post">
    @csrf
    <input type="hidden" name="modalFolderId" x-model="folderId" value="{{ $folder_id }}" readonly>
    {{-- <p>
        {{ __('Delete all files inside the folder') }}
    </p> --}}
    <x-forms.input class="m-0" class:label="text-base font-medium text-foreground"
        label="{{ __('Delete all files inside the folder') }}" type="checkbox" name="deleteAll" x-model="deleteAll"
        value="deleteAll" />
    <div class="border-t pt-3 text-end">
        <x-button @click.prevent="modalOpen = false" variant="outline" hover-variant="primary">
            {{ __('Cancel') }}
        </x-button>
        <x-button tag="button" variant="danger" type="submit">
            {{ __('Delete') }}
        </x-button>
    </div>
</form>
