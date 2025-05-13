<div
        class="hidden"
        :class="{ 'hidden': activeTab !== 'pdf' }"
>
    <div
            class="mb-4 flex w-full items-center justify-center"
            id="mainupscale_src"
            ondrop="dropHandler(event, 'upscale_src');"
            ondragover="dragOverHandler(event);"
    >
        <label
                class="min-h-56 group flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-foreground/10 bg-background text-center text-[12px] transition-colors hover:bg-background/80"
                for="upscale_src"
        >
            <div class="flex flex-col items-center justify-center py-6">
                <x-tabler-circle-plus
                        class="size-11 mb-3.5"
                        stroke-width="1"
                />

                <p class="mb-1 font-semibold">
                    {{ __('PDF, DOC Upload') }}
                </p>

                <p class="file-name mb-0">
                    {{ __('File Upload (Max: 25Mb)') }}
                </p>
            </div>

            <input
                    class="hidden"
                    id="upscale_src"
                    name="files[]"
                    type="file"
                    multiple
                    onchange="handlePdfFileSelect(event)"
            />
        </label>
    </div>

    <div
            class="pdf-list space-y-4"
            id="pdf-list"
    >

    </div>

    <div id="existing-files-hidden-inputs"></div>
</div>

@push("script")
    <script>
        let files = [];

        @if(!empty($existingFiles))
            files = {!! json_encode(array_map(function($file) {
            return [
                'name' => $file['filename'],
                'id' => $file['id']
            ];
        }, $existingFiles)) !!};
        renderFileList();
        createHiddenInputsForExistingFiles();
        @endif

        function handlePdfFileSelect(event) {
            const selectedFiles = event.target.files;
            for (let i = 0; i < selectedFiles.length; i++) {
                const existingFileIndex = files.findIndex(file => file.name === selectedFiles[i].name);
                if (existingFileIndex === -1) {
                    files.push({
                        name: selectedFiles[i].name,
                        id: null
                    });
                    addFileToList({name: selectedFiles[i].name});
                }
            }
        }

        function addFileToList(file) {
            const fileList = document.getElementById('pdf-list');
            const fileItem = document.createElement('div');
            fileItem.className = 'item flex items-center justify-between rounded-lg border p-1.5';

            const fileName = typeof file.name === 'string' ? file.name : 'Bilinmeyen Dosya';

            fileItem.innerHTML = `
            <span>${fileName}</span>
            <button type="button" onclick="removeFile('${fileName}')" class="text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                </svg>
                <span class="sr-only">{{ __('Delete') }}</span>
            </button>
        `;
            fileList.appendChild(fileItem);
        }

        function removeFile(fileName) {
            files = files.filter(file => file.name !== fileName);
            renderFileList();
            createHiddenInputsForExistingFiles();
        }

        function renderFileList() {
            const fileList = document.getElementById('pdf-list');
            fileList.innerHTML = '';
            files.forEach(file => addFileToList(file));
        }

        function createHiddenInputsForExistingFiles() {
            const container = document.getElementById('existing-files-hidden-inputs');
            container.innerHTML = '';

            files.forEach(file => {
                if (file.id) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'existingfiles[]';
                    input.value = file.id;
                    container.appendChild(input);
                }
            });
        }
    </script>
@endpush


