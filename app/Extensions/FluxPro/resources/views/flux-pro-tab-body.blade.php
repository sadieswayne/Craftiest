
<div
        class="lqd-image-generator-tabs-content lqd-image-generator-stablediffusion hidden"
        x-data="{
            activeTool: 'text-to-image',
            setActiveTool(tool) {
                if (this.activeTool === tool) return;
                if (!document.startViewTransition) {
                    return this.activeTool = tool;
                }
                document.startViewTransition(() => this.activeTool = tool);
            }
        }"
        :class="{ 'hidden': activeGenerator !== 'flux-pro' }"
>

    <form
            class="lqd-image-generator-dalle-form flex flex-col items-start gap-4"
            id="openai_generator_form"
            onsubmit="return sendFluxProGeneratorForm();"
            x-data="{ advancedSettingsShow: false }"
    >
        <h3
                class="flex w-full flex-wrap items-center gap-2"
                :class="{ 'hidden': activeGenerator !== 'flux-pro' }"
        >
            {{ __('Explain your idea') }}. |
            <button
                    class="lqd-image-generator-random-prompt-trigger cursor-pointer text-green-600 hover:underline"
                    type="button"
                    x-data
                    @click="prompt = generateRandomPrompt()"
            >
                {{ __('Generate example prompt') }}
            </button>
            @if (setting('user_ai_image_prompt_library') == null || setting('user_ai_image_prompt_library'))
                <button
                        class="lqd-generator-templates-trigger size-10 flex shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-heading-background md:hover:text-heading-foreground"
                        type="button"
                        @click.prevent="togglePromptLibraryShow()"
                >
                    <x-tabler-article
                            class="size-6"
                            stroke-width="1.5"
                    />
                    <span class="md:hidden">{{ __('Browse prompt library') }}</span>
                </button>
            @endif


        </h3>

        <div class="lqd-image-generator-inputs-wrap relative w-full">
            <x-forms.input
                    class="lqd-image-generator-prompt max-md:min-h-32 h-14 resize-none overflow-hidden rounded-full bg-background px-6 py-4 text-heading-foreground shadow-sm placeholder:text-foreground/50 max-md:rounded-md"
                    id="description_flux_pro"
                    type="textarea"
                    name="description"
                    x-data
                    ::value="prompt"
                    ::placeholder="generateRandomPrompt()"
            />
            <x-button
                    class="absolute end-4 top-1/2 -translate-y-1/2 hover:-translate-y-1/2 hover:scale-110 max-lg:relative max-lg:right-auto max-lg:top-auto max-lg:mt-2 max-lg:w-full max-lg:translate-y-0"
                    id="flux_generator_button"
                    tag="button"
                    type="submit"
            >
                {{ __('Generate') }}
                <x-tabler-arrow-right class="size-5"/>
            </x-button>
        </div>
    </form>
</div>