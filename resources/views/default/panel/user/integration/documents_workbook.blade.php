@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Workbook'))
@section('titlebar_pretitle', __('Share post your integrations.'))
@section('titlebar_title', $title)
@section('titlebar_actions', '')

@section('settings')
    <div class="py-10">
        <div class="[&_.tox-edit-area__iframe]:!bg-transparent">
            @if ($workbook->generator->type === 'code')
                <input
                    id="code_lang"
                    type="hidden"
                    value="{{ substr($workbook->input, strrpos($workbook->input, 'in') + 3) }}"
                >
                <div class="mt-4 min-h-full border-t pt-6">
                    <div
                        class="line-numbers min-h-full resize [direction:ltr] [&_kbd]:inline-flex [&_kbd]:rounded [&_kbd]:bg-primary/10 [&_kbd]:px-1 [&_kbd]:py-0.5 [&_kbd]:font-semibold [&_kbd]:text-primary [&_pre[class*=language]]:my-4 [&_pre[class*=language]]:rounded"
                        id="code-pre"
                    >
                        <div
                            class="prose dark:prose-invert"
                            id="code-output"
                        >{{ $workbook->output }}</div>
                    </div>
                </div>
            @elseif($workbook->generator->type === 'image')
                <form
                    class="workbook-form group/form flex flex-col gap-6"
                    method="POST"
                    action="{{ route('dashboard.user.integration.share.image', [$userIntegration->id, $workbook->id]) }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="image"
                        value="{{ $workbook->output }}"
                    >

                    <figure>
                        <a href="{{ $workbook->output }}">
                            <img
                                class="rounded-xl shadow-xl"
                                src="{{ custom_theme_url($workbook->output) }}"
                                alt="{{ __($workbook->generator->title) }}"
                            />
                        </a>
                    </figure>

                    <x-button
                        class="w-full"
                        id="share"
                        tag="button"
                        type="submit"
                        variant="primary"
                        size="lg"
                    >
                        <span class="group-[&.loading]/form:hidden">{{ __('Share') }}</span>
                        <span class="hidden group-[&.loading]/form:inline-block">{{ __('Please wait...') }}</span>
                    </x-button>

                </form>
            @elseif(in_array($workbook->generator->type, ['text', 'youtube', 'rss', 'audio']))
                <form
                    class="workbook-form group/form flex flex-col gap-6"
                    method="POST"
                    action="{{ route('dashboard.user.integration.share.workbook', [$userIntegration->id, $workbook->id]) }}"
                >
                    @csrf
                    <x-forms.input
                        class="border-transparent font-serif text-2xl"
                        id="workbook_title"
                        name="title"
                        placeholder="{{ __('Untitled Document...') }}"
                        value="{{ $workbook->title }}"
                    />
                    <x-forms.input
                        class="tinymce border-0 font-body"
                        id="content"
                        name="workbook_text"
                        type="textarea"
                        rows="25"
                    >{!! $workbook->output !!}
                    </x-forms.input>

                    <div class="flex flex-wrap">
                        <div class="mt-4 w-full px-4">
                            <x-forms.input
                                id="comment_status"
                                type="select"
                                name="comment_status"
                                size="none"
                                label="{{ __('Comment Status') }}"
                            >
                                <option
                                    value="open"
                                    selected
                                >Open</option>
                                <option value="close">Close</option>
                            </x-forms.input>
                        </div>
                        <div class="mt-4 w-full px-4">
                            <x-forms.input
                                id="status"
                                type="select"
                                name="status"
                                size="none"
                                label="{{ __('Post Status') }}"
                            >
                                <option
                                    value="publish"
                                    selected
                                >Public</option>
                                <option value="private">Private</option>
                            </x-forms.input>
                        </div>
                        @if ($categories != null)
                            <div class="mt-4 w-full px-4">
                                <x-forms.input
                                    id="categories"
                                    type="select"
                                    name="categories[]"
                                    multiple
                                    size="none"
                                    label="{{ __('Category') }}"
                                >

                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat['id'] }}">
                                            {{ $cat['name'] }}
                                        </option>
                                    @endforeach

                                </x-forms.input>
                            </div>
                        @endif
                        @if ($tags != null)
                            <div class="mt-4 w-full px-4">
                                <x-forms.input
                                    id="tags"
                                    type="select"
                                    name="tags[]"
                                    multiple
                                    size="none"
                                    label="{{ __('Tag') }}"
                                >

                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag['id'] }}">
                                            {{ $tag['name'] }}
                                        </option>
                                    @endforeach

                                </x-forms.input>
                            </div>
                        @endif

                        <div class="mt-4 w-full px-4">
                            <x-forms.input
                                id="date_gmt"
                                type="datetime-local"
                                name="date_gmt"
                                size="none"
                                label="{{ __('Publish Date') }}"
                            >
                            </x-forms.input>
                        </div>

                    </div>

                    @if ($images != null)
                        <div class="mt-4 w-full px-4">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                for="featured_media"
                            >{{ __('Featured Media') }}</label>
                            <div
                                class="relative"
                                x-data="{ open: false, selectedImage: null }"
                            >
                                <div
                                    class="cursor-pointer rounded border p-2"
                                    @click="open = !open"
                                    :class="{ 'border-blue-500': open }"
                                >
                                    <template x-if="selectedImage">
                                        <div class="flex items-center">
                                            <img
                                                class="mr-2 h-10 w-10"
                                                :src="selectedImage.source_url"
                                                alt=""
                                            >
                                            <span x-text="selectedImage.title.rendered"></span>
                                        </div>
                                    </template>
                                    <template x-if="!selectedImage">
                                        <span>{{ __('Select an image') }}</span>
                                    </template>
                                </div>
                                <div
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto border bg-white"
                                    x-show="open"
                                    @click.away="open = false"
                                >
                                    <div class="grid grid-cols-2 gap-4 p-2">
                                        @foreach ($images as $image)
                                            <div
                                                class="flex cursor-pointer items-center hover:bg-gray-100"
                                                @click="selectedImage = {{ json_encode($image) }}; open = false"
                                            >
                                                <img
                                                    class="h-20 w-20 object-cover"
                                                    src="{{ $image['source_url'] }}"
                                                    alt="{{ $image['title']['rendered'] }}"
                                                >
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <input
                                    type="hidden"
                                    name="featured_media"
                                    :value="selectedImage ? selectedImage.id : ''"
                                >
                            </div>
                        </div>

                    @endif

                    <x-button
                        class="w-full"
                        id="share"
                        tag="button"
                        type="submit"
                        variant="primary"
                        size="lg"
                    >
                        <span class="group-[&.loading]/form:hidden">{{ __('Share') }}</span>
                        <span class="hidden group-[&.loading]/form:inline-block">{{ __('Please wait...') }}</span>
                    </x-button>

            @endif
        </div>
    </div>
@endsection
@php
    $lang_with_flags = [];
    foreach (LaravelLocalization::getSupportedLocales() as $lang => $properties) {
        $lang_with_flags[] = [
            'lang' => $lang,
            'name' => $properties['native'],
            'flag' => country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)),
        ];
    }
@endphp

@push('script')

    <script>
        const lang_with_flags = @json($lang_with_flags);
    </script>

    <script src="{{ custom_theme_url('/assets/libs/beautify-html.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ext-language_tools.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/markdown-it.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/turndown.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/tinymce-theme-handler.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/workbook.js') }}"></script>

    @if ($openai->type == 'code')
        <link
            rel="stylesheet"
            href="{{ custom_theme_url('/assets/libs/prism/prism.css') }}"
        />
        <script src="{{ custom_theme_url('/assets/libs/prism/prism.js') }}"></script>
        <script>
            window.Prism = window.Prism || {};
            window.Prism.manual = true;
            document.addEventListener('DOMContentLoaded', (event) => {
                "use strict";

                const codeLang = document.querySelector('#code_lang');
                const codePre = document.querySelector('#code-pre');
                const codeOutput = codePre?.querySelector('#code-output');

                if (!codeOutput) return;

                codePre.classList.add(`language-${codeLang && codeLang.value !== '' ? codeLang.value : 'javascript'}`);

                // saving for copy
                window.codeRaw = codeOutput.innerText;

                Prism.highlightElement(codeOutput);
            });
        </script>
    @endif
@endpush
