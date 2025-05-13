<x-card
    class="lqd-voiceover-generator mb-8 bg-[#F2F1FD] shadow-sm dark:bg-foreground/5"
    class:body="pt-3"
    size="lg"
>
    <form
        class="workbook-form flex flex-col gap-8"
        id="openai_generator_form"
        onsubmit="return sendOpenaiGeneratorForm();"
    >

        <div
            class="flex w-full flex-col gap-5"
            ondrop="dropHandler(event, 'file');"
            ondragover="dragOverHandler(event);"
        >
            <label
                class="lqd-filepicker-label min-h-64 mt-5 flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-foreground/10 bg-background text-center transition-colors hover:bg-background/80"
                for="file"
            >
                <div class="flex flex-col items-center justify-center py-6">
                    <x-tabler-volume
                        class="size-8 mx-auto mb-2"
                        stroke-width="1.5"
                    />
                    <p class="m-0 font-medium">
                        <span class="opacity-70">
                            @lang('Drag and drop an audio file')
                        </span>
                    </p>
                    <p class="file-name mb-0 text-2xs">
                        @lang('or click here to browse your files.')
                    </p>
                </div>

                @foreach (json_decode($openai->questions) ?? [] as $question)
                    <x-forms.input
                        class="hidden"
                        class:label="static text-center text-heading-foreground/40 text-3xs lg:px-10 before:absolute before:top-0 before:start-0 before:w-full before:h-full before:block before:z-2"
                        id="file"
                        container-class="static"
                        size="lg"
                        label="{{ __($question->question) }}"
                        name="{{ $question->name }}"
                        type="file"
                        accept="audio/*"
                        placeholder="{{ __($question->question) }}"
                        onchange="handleFileSelect('file')"
                    />
                @endforeach
            </label>
        </div>

        <div class="flex w-full">
            <x-button
                class="z-50 w-full py-2.5"
                id="openai_generator_button"
                tag="button"
                type="submit"
                size="lg"
                form="openai_generator_form"
            >
                <x-tabler-plus class="size-5" />
                {{ __('Isolate Voice') }}
            </x-button>
        </div>
    </form>
</x-card>
<h2 class="font-bold">
    @lang('Audio Files')
</h2>
<x-card
    class="w-full [&_.tox-edit-area__iframe]:!bg-transparent"
    id="generator_sidebar_table"
    variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
    size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md') }}"
    roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
>
    @include('panel.user.openai.components.generator_sidebar_table')
</x-card>
