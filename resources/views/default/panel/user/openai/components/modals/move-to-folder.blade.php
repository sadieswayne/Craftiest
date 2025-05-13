<form
    action="{{ route('dashboard.user.openai.documents.move-to-folder') }}"
    method="post"
>
    @csrf
    <input
        type="hidden"
        name="fileslug"
        value="{{ $file_slug }}"
    />
    <x-forms.input
        type="select"
        name="selectedFolderId"
        label="{{ __('Select Folder:') }}"
        required
    >
		@php
			$folders = auth()->user()->folders()->get();
		@endphp
		@foreach ($folders as $folder)
			<option value="{{ $folder->id }}">{{ $folder->name }}</option>
		@endforeach
	</x-forms.input>
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
            {{ __('Move') }}
        </x-button>
    </div>
</form>
