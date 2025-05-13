<x-card
        class="lqd-voiceover-generator mb-16 bg-[#F2F1FD] shadow-sm dark:bg-foreground/5"
        class:body="pt-3"
        size="lg"
>
    <form
            class="workbook-form flex flex-col gap-8"
            id="openai_generator_form"
            onsubmit="return generateSpeech();"
    >
        <x-forms.input
                class="rounded-none border-e-0 border-s-0 border-t-0 border-foreground/10 bg-transparent px-0 py-7 font-serif text-xl focus:outline-none focus:ring-0"
                id="workbook_title"
                placeholder="{{ __('Untitled Voice...') }}"
                value="{{ __('Untitled Voice...') }}"
                required
        />

        <div class="flex flex-wrap justify-between gap-3">
            <x-forms.input
                    class="bg-background text-heading-foreground focus:ring-heading-foreground/5"
                    class:label="text-heading-foreground"
                    id="languages"
                    container-class="grow"
                    tooltip="{{ __('Select speech language') }}"
                    name="languages"
                    label="{{ __('Language') }}"
                    size="lg"
                    type="select"
            >
                @if ($settings_two->tts == 'openai')
                    <option
                            abbr="Afr"
                            value="afrikaans"
                    >
                        {{ __('Afrikaans') }}
                    </option>
                    <option
                            abbr="Ar"
                            value="arabic"
                    >
                        {{ __('Arabic') }}
                    </option>
                    <option
                            abbr="Hy "
                            value="armenian"
                    >
                        {{ __('Armenian') }}
                    </option>
                    <option
                            abbr="Az"
                            value="azerbaijani"
                    >
                        {{ __('Azerbaijani') }}
                    </option>
                    <option
                            abbr="Be"
                            value="belarusian"
                    >
                        {{ __('Belarusian') }}
                    </option>
                    <option
                            abbr="Bs"
                            value="bosnian"
                    >
                        {{ __('Bosnian') }}
                    </option>
                    <option
                            abbr="Bg"
                            value="bulgarian"
                    >
                        {{ __('Bulgarian') }}
                    </option>
                    <option
                            abbr="Ca"
                            value="catalan"
                    >
                        {{ __('Catalan') }}
                    </option>
                    <option
                            abbr="Zh"
                            value="chinese"
                    >
                        {{ __('Chinese') }}
                    </option>
                    <option
                            abbr="Hr "
                            value="croatian"
                    >
                        {{ __('Croatian') }}
                    </option>
                    <option
                            abbr="Cs"
                            value="czech"
                    >
                        {{ __('Czech') }}
                    </option>
                    <option
                            abbr="Da"
                            value="danish"
                    >
                        {{ __('Danish') }}
                    </option>
                    <option
                            abbr="Nl"
                            value="dutch"
                    >
                        {{ __('Dutch') }}
                    </option>
                    <option
                            abbr="En"
                            value="english"
                            selected
                    >
                        {{ __('English') }}
                    </option>
                    <option
                            abbr="Et"
                            value="estonian"
                    >
                        {{ __('Estonian') }}
                    </option>
                    <option
                            abbr="Fi"
                            value="finnish"
                    >
                        {{ __('Finnish') }}
                    </option>
                    <option
                            abbr="Fr"
                            value="french"
                    >
                        {{ __('French') }}
                    </option>
                    <option
                            abbr="Gl"
                            value="galician"
                    >
                        {{ __('Galician') }}
                    </option>
                    <option
                            abbr="De"
                            value="german"
                    >
                        {{ __('German') }}
                    </option>
                    <option
                            abbr="El"
                            value="greek"
                    >
                        {{ __('Greek') }}
                    </option>
                    <option
                            abbr="He"
                            value="hebrew"
                    >
                        {{ __('Hebrew') }}
                    </option>
                    <option
                            abbr="Hi"
                            value="hindi"
                    >
                        {{ __('Hindi') }}
                    </option>
                    <option
                            abbr="Hu"
                            value="hungarian"
                    >
                        {{ __('Hungarian') }}
                    </option>
                    <option
                            abbr="Is"
                            value="icelandic"
                    >
                        {{ __('Icelandic') }}
                    </option>
                    <option
                            abbr="Id"
                            value="indonesian"
                    >
                        {{ __('Indonesian') }}
                    </option>
                    <option
                            abbr="It"
                            value="italian"
                    >
                        {{ __('Italian') }}
                    </option>
                    <option
                            abbr="Ja"
                            value="japanese"
                    >
                        {{ __('Japanese') }}
                    </option>
                    <option
                            abbr="Kn"
                            value="kannada"
                    >
                        {{ __('Kannada') }}
                    </option>
                    <option
                            abbr="Kz"
                            value="kazakh"
                    >
                        {{ __('Kazakh') }}
                    </option>
                    <option
                            abbr="Ko"
                            value="korean"
                    >
                        {{ __('Korean') }}
                    </option>
                    <option
                            abbr="Lv"
                            value="latvian"
                    >
                        {{ __('Latvian') }}
                    </option>
                    <option
                            abbr="Lt"
                            value="lithuanian"
                    >
                        {{ __('Lithuanian') }}
                    </option>
                    <option
                            abbr="Mk"
                            value="macedonian"
                    >
                        {{ __('Macedonian') }}
                    </option>
                    <option
                            abbr="Ms"
                            value="malay"
                    >
                        {{ __('Malay') }}
                    </option>
                    <option
                            abbr="Mr"
                            value="marathi"
                    >
                        {{ __('Marathi') }}
                    </option>
                    <option
                            abbr="Ml"
                            value="maori"
                    >
                        {{ __('Maori') }}
                    </option>
                    <option
                            abbr="Ne"
                            value="nepali"
                    >
                        {{ __('Nepali') }}
                    </option>
                    <option
                            abbr="No"
                            value="norwegian"
                    >
                        {{ __('Norwegian') }}
                    </option>
                    <option
                            abbr="Fa"
                            value="persian"
                    >
                        {{ __('Persian') }}
                    </option>
                    <option
                            abbr="Pl"
                            value="polish"
                    >
                        {{ __('Polish') }}
                    </option>
                    <option
                            abbr="Pt"
                            value="portuguese"
                    >
                        {{ __('Portuguese') }}
                    </option>
                    <option
                            abbr="Ro"
                            value="romanian"
                    >
                        {{ __('Romanian') }}
                    </option>
                    <option
                            abbr="Ru"
                            value="russian"
                    >
                        {{ __('Russian') }}
                    </option>
                    <option
                            abbr="Sr"
                            value="serbian"
                    >
                        {{ __('Serbian') }}
                    </option>
                    <option
                            abbr="Sk"
                            value="slovak"
                    >
                        {{ __('Slovak') }}
                    </option>
                    <option
                            abbr="Sl"
                            value="slovenian"
                    >
                        {{ __('Slovenian') }}
                    </option>
                    <option
                            abbr="Es"
                            value="spanish"
                    >
                        {{ __('Spanish') }}
                    </option>
                    <option
                            abbr="Sw"
                            value="swahili"
                    >
                        {{ __('Swahili') }}
                    </option>
                    <option
                            abbr="Sv"
                            value="swedish"
                    >
                        {{ __('Swedish') }}
                    </option>
                    <option
                            abbr="Tl"
                            value="tagalog"
                    >
                        {{ __('Tagalog') }}
                    </option>
                    <option
                            abbr="Ta"
                            value="tamil"
                    >
                        {{ __('Tamil') }}
                    </option>
                    <option
                            abbr="Th"
                            value="thai"
                    >
                        {{ __('Thai') }}
                    </option>
                    <option
                            abbr="Tr"
                            value="turkish"
                    >
                        {{ __('Turkish') }}
                    </option>
                    <option
                            abbr="Uk"
                            value="ukrainian"
                    >
                        {{ __('Ukrainian') }}
                    </option>
                    <option
                            abbr="Ur"
                            value="urdu"
                    >
                        {{ __('Urdu') }}
                    </option>
                    <option
                            abbr="Vi"
                            value="vietnamese"
                    >
                        {{ __('Vietnamese') }}
                    </option>
                    <option
                            abbr="Cy"
                            value="welsh"
                    >
                        {{ __('Welsh') }}
                    </option>
                @else
                    <option
                            language="Afrikaans"
                            @selected(LaravelLocalization::getCurrentLocale() == 'af')
                            value="af-ZA"
                    >
                        {{ __('Afrikaans (South Africa)') }}
                    </option>
                    <option
                            language="Arabic"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ar')
                            value="ar-XA"
                    >
                        {{ __('Arabic') }}
                    </option>
                    <option
                            language="Basque"
                            @selected(LaravelLocalization::getCurrentLocale() == 'eu')
                            value="eu-ES"
                    >
                        {{ __('Basque (Spain)') }}
                    </option>
                    <option
                            language="Bengali"
                            @selected(LaravelLocalization::getCurrentLocale() == 'bn')
                            value="bn-IN"
                    >
                        {{ __('Bengali (India)') }}
                    </option>
                    <option
                            language="Bulgarian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'bg')
                            value="bg-BG"
                    >
                        {{ __('Bulgarian (Bulgaria)') }}
                    </option>
                    <option
                            language="Catalan"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ca')
                            value="ca-ES"
                    >
                        {{ __('Catalan (Spain) ') }}
                    </option>
                    <option
                            language="Chinese"
                            @selected(LaravelLocalization::getCurrentLocale() == 'yue')
                            value="yue-HK"
                    >
                        {{ __('Chinese (Hong Kong)') }}
                    </option>
                    <option
                            language="Chinese"
                            @selected(LaravelLocalization::getCurrentLocale() == 'zh')
                            value="zh-CN"
                    >
                        {{ __('Chinese (Mandarin, Simplified)') }}
                    </option>
                    <option
                            language="Czech"
                            @selected(LaravelLocalization::getCurrentLocale() == 'cs')
                            value="cs-CZ"
                    >
                        {{ __('Czech (Czech Republic)') }}
                    </option>
                    <option
                            language="Danish"
                            @selected(LaravelLocalization::getCurrentLocale() == 'da')
                            value="da-DK"
                    >
                        {{ __('Danish (Denmark)') }}
                    </option>
                    <option
                            language="Dutch"
                            @selected(LaravelLocalization::getCurrentLocale() == 'nl')
                            value="nl-BE"
                    >
                        {{ __('Dutch (Belgium)') }}
                    </option>
                    <option
                            language="Dutch"
                            value="nl-NL"
                    >
                        {{ __('Dutch (Netherlands)') }}
                    </option>
                    <option
                            language="English"
                            value="en-AU"
                    >
                        {{ __('English (Australia)') }}
                    </option>
                    <option
                            language="English"
                            value="en-IN"
                    >
                        {{ __('English (India)') }}
                    </option>
                    <option
                            language="English"
                            value="en-GB"
                    >
                        {{ __('English (UK)') }}
                    </option>
                    <option
                            language="English"
                            @selected(LaravelLocalization::getCurrentLocale() == 'en')
                            value="en-US"
                    >
                        {{ __('English (US)') }}
                    </option>
                    <option
                            language="Filipino"
                            @selected(LaravelLocalization::getCurrentLocale() == 'fil')
                            value="fil-PH"
                    >
                        {{ __('Filipino (Philippines)') }}
                    </option>
                    <option
                            language="Finnish"
                            @selected(LaravelLocalization::getCurrentLocale() == 'fi')
                            value="fi-FI"
                    >
                        {{ __('Finnish (Finland)') }}
                    </option>
                    <option
                            language="French"
                            value="fr-CA"
                    >
                        {{ __('French (Canada)') }}
                    </option>
                    <option
                            language="French"
                            @selected(LaravelLocalization::getCurrentLocale() == 'fr')
                            value="fr-FR"
                    >
                        {{ __('French (France)') }}
                    </option>
                    <option
                            language="Galician"
                            @selected(LaravelLocalization::getCurrentLocale() == 'gl')
                            value="gl-ES"
                    >
                        {{ __('Galician (Spain)') }}
                    </option>
                    <option
                            language="German"
                            @selected(LaravelLocalization::getCurrentLocale() == 'de')
                            value="de-DE"
                    >
                        {{ __('German (Germany)') }}
                    </option>
                    <option
                            language="Greek"
                            @selected(LaravelLocalization::getCurrentLocale() == 'el')
                            value="el-GR"
                    >
                        {{ __('Greek (Greece)') }}
                    </option>
                    <option
                            language="Gujarati"
                            @selected(LaravelLocalization::getCurrentLocale() == 'gu')
                            value="gu-IN"
                    >
                        {{ __('Gujarati (India)') }}
                    </option>
                    <option
                            language="Hebrew"
                            @selected(LaravelLocalization::getCurrentLocale() == 'he')
                            value="he-IL"
                    >
                        {{ __('Hebrew (Israel)') }}
                    </option>
                    <option
                            language="Hindi"
                            @selected(LaravelLocalization::getCurrentLocale() == 'hi')
                            value="hi-IN"
                    >
                        {{ __('Hindi (India)') }}
                    </option>
                    <option
                            language="Hungarian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'hu')
                            value="hu-HU"
                    >
                        {{ __('Hungarian (Hungary)') }}
                    </option>
                    <option
                            language="Icelandic"
                            @selected(LaravelLocalization::getCurrentLocale() == 'is')
                            value="is-IS"
                    >
                        {{ __('Icelandic (Iceland)') }}
                    </option>
                    <option
                            language="Indonesian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'id')
                            value="id-ID"
                    >
                        {{ __('Indonesian (Indonesia)') }}
                    </option>
                    <option
                            language="Italian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'it')
                            value="it-IT"
                    >
                        {{ __('Italian (Italy)') }}
                    </option>
                    <option
                            language="Japanese"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ja')
                            value="ja-JP"
                    >
                        {{ __('Japanese (Japan)') }}
                    </option>
                    <option
                            language="Kannada"
                            @selected(LaravelLocalization::getCurrentLocale() == 'kn')
                            value="kn-IN"
                    >
                        {{ __('Kannada (India)') }}
                    </option>
                    <option
                            language="Korean (Sout"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ko')
                            value="ko-KR"
                    >
                        {{ __('Korean (South Korea)') }}
                    </option>
                    <option
                            language="Latvian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'lv')
                            value="lv-LV"
                    >
                        {{ __('Latvian (Latvia)') }}
                    </option>
                    <option
                            language="Malay"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ms')
                            value="ms-MY"
                    >
                        {{ __('Malay (Malaysia)') }}
                    </option>
                    <option
                            language="Malayalam"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ml')
                            value="ml-IN"
                    >
                        {{ __('Malayalam (India)') }}
                    </option>
                    <option
                            language="Mandari"
                            @selected(LaravelLocalization::getCurrentLocale() == 'cmn')
                            value="cmn-CN"
                    >
                        {{ __('Mandarin Chinese') }}
                    </option>
                    <option
                            language="Mandarin Chinese"
                            value="cmn-TW"
                    >
                        {{ __('Mandarin Chinese (T)') }}
                    </option>
                    <option
                            language="Marathi"
                            @selected(LaravelLocalization::getCurrentLocale() == 'mr')
                            value="mr-IN"
                    >
                        {{ __('Marathi (India)') }}
                    </option>
                    <option
                            language="Norwegian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'nb')
                            value="nb-NO"
                    >
                        {{ __('Norwegian (Norway)') }}
                    </option>
                    <option
                            language="Polish"
                            @selected(LaravelLocalization::getCurrentLocale() == 'pl')
                            value="pl-PL"
                    >
                        {{ __('Polish (Poland)') }}
                    </option>
                    <option
                            language="Portuguese"
                            @selected(LaravelLocalization::getCurrentLocale() == 'pt')
                            value="pt-BR"
                    >
                        {{ __('Portuguese (Brazil)') }}
                    </option>
                    <option
                            language="Portuguese"
                            value="pt-PT"
                    >
                        {{ __('Portuguese (Portugal)') }}
                    </option>
                    <option
                            language="Punjabi"
                            @selected(LaravelLocalization::getCurrentLocale() == 'pa')
                            value="pa-IN"
                    >
                        {{ __('Punjabi (India)') }}
                    </option>
                    <option
                            language="Romanian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ro')
                            value="ro-RO"
                    >
                        {{ __('Romanian (Romania)') }}
                    </option>
                    <option
                            language="Russian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ru')
                            value="ru-RU"
                    >
                        {{ __('Russian (Russia)') }}
                    </option>
                    <option
                            language="Serbian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'sr')
                            value="sr-RS"
                    >
                        {{ __('Serbian (Cyrillic)') }}
                    </option>
                    <option
                            language="Slovak"
                            @selected(LaravelLocalization::getCurrentLocale() == 'sk')
                            value="sk-SK"
                    >
                        {{ __('Slovak (Slovakia)') }}
                    </option>
                    <option
                            language="Spanish"
                            @selected(LaravelLocalization::getCurrentLocale() == 'es')
                            value="es-ES"
                    >
                        {{ __('Spanish (Spain)') }}
                    </option>
                    <option
                            language="Spanish"
                            @selected(LaravelLocalization::getCurrentLocale() == 'es')
                            value="es-US"
                    >
                        {{ __('Spanish (US)') }}
                    </option>
                    <option
                            language="Swedish"
                            @selected(LaravelLocalization::getCurrentLocale() == 'sv')
                            value="sv-SE"
                    >
                        {{ __('Swedish (Sweden)') }}
                    </option>
                    <option
                            language="Tamil"
                            @selected(LaravelLocalization::getCurrentLocale() == 'ta')
                            value="ta-IN"
                    >
                        {{ __('Tamil (India)') }}
                    </option>
                    <option
                            language="Telugu"
                            @selected(LaravelLocalization::getCurrentLocale() == 'te')
                            value="te-IN"
                    >
                        {{ __('Telugu (India)') }}
                    </option>
                    <option
                            language="Thai"
                            @selected(LaravelLocalization::getCurrentLocale() == 'th')
                            value="th-TH"
                    >
                        {{ __('Thai (Thailand)') }}
                    </option>
                    <option
                            language="Turkish"
                            @selected(LaravelLocalization::getCurrentLocale() == 'tr')
                            value="tr-TR"
                    >
                        {{ __('Turkish (Turkey)') }}
                    </option>
                    <option
                            language="Ukrainian"
                            @selected(LaravelLocalization::getCurrentLocale() == 'uk')
                            value="uk-UA"
                    >
                        {{ __('Ukrainian (Ukraine)') }}
                    </option>
                    <option
                            language="Vietnamese"
                            @selected(LaravelLocalization::getCurrentLocale() == 'vi')
                            value="vi-VN"
                    >
                        {{ __('Vietnamese (Vietnam)') }}
                    </option>
                @endif
            </x-forms.input>

            @if ($settings_two->tts == 'openai')
                <x-forms.input
                        class="bg-background text-heading-foreground focus:ring-heading-foreground/5"
                        class:label="text-heading-foreground"
                        id="voice_openai"
                        container-class="grow"
                        tooltip="{{ __('You can select speech voice') }}"
                        name="voice"
                        label="{{ __('Voice') }}"
                        size="lg"
                        type="select"
                >
                    <option value="alloy">{{ __('Alloy') }}</option>
                    <option value="echo">{{ __('Echo') }}</option>
                    <option value="fable">{{ __('Fable') }}</option>
                    <option value="onyx">{{ __('Onyx') }}</option>
                    <option value="nova">{{ __('Nova') }}</option>
                    <option value="shimmer">{{ __('shimmer') }}</option>
                </x-forms.input>

                <x-forms.input
                        class="bg-background text-heading-foreground focus:ring-heading-foreground/5"
                        class:label="text-heading-foreground"
                        id="model_openai"
                        container-class="grow"
                        tooltip="{{ __('You can select speech model') }}"
                        name="model_openai"
                        label="{{ __('Model') }}"
                        size="lg"
                        type="select"
                >
                    <option value="{{\App\Domains\Entity\Enums\EntityEnum::TTS_1->value}}">{{\App\Domains\Entity\Enums\EntityEnum::TTS_1->label()}}</option>
                    <option value="{{\App\Domains\Entity\Enums\EntityEnum::TTS_1_HD->value}}">{{ \App\Domains\Entity\Enums\EntityEnum::TTS_1_HD->label() }}</option>
                </x-forms.input>
            @else
                <x-forms.input
                        class="bg-background text-heading-foreground focus:ring-heading-foreground/5"
                        class:label="text-heading-foreground"
                        id="voice"
                        container-class="grow"
                        tooltip="{{ __('You can select speech voice. Female, Male, and type') }}"
                        name="voice"
                        label="{{ __('Voice') }}"
                        size="lg"
                        type="select"
                >
                    <option value="">{{ __('Select a voice') }}</option>
                </x-forms.input>

                <x-button
                    class="lqd-video-result-play size-9 shrink-0 self-end rounded-full bg-background text-foreground hover:bg-primary hover:text-primary-foreground"
                    id="playVoiceButton"
                    data-fslightbox="video-gallery"
                    style="display: none"
                    size="none"
                >
                    <x-tabler-player-play-filled class="size-3" />
                </x-button>

                <x-forms.input
                        class="bg-background text-heading-foreground focus:ring-heading-foreground/5"
                        class:label="text-heading-foreground"
                        id="pace"
                        container-class="grow"
                        tooltip="{{ __('Speech speed') }}"
                        name="pace"
                        label="{{ __('Pace') }}"
                        size="lg"
                        type="select"
                >
                    <option value="x-slow">
                        {{ __('Very Slow') }}
                    </option>
                    <option value="slow">
                        {{ __('Slow') }}
                    </option>
                    <option
                            value="medium"
                            selected
                    >
                        {{ __('Medium') }}
                    </option>
                    <option value="fast">
                        {{ __('Fast') }}
                    </option>
                    <option value="x-fast">
                        {{ __('Ultra Fast') }}
                    </option>
                </x-forms.input>

                <x-forms.input
                        class="bg-background text-heading-foreground focus:ring-heading-foreground/5"
                        class:label="text-heading-foreground"
                        id="break"
                        container-class="grow"
                        tooltip="{{ __('Wait x seconds after the speech. Represents the time before the next sentence.') }}"
                        name="break"
                        label="{{ __('Pause') }}"
                        size="lg"
                        type="select"
                >
                    <option value="0">
                        {{ __('0s') }}
                    </option>
                    <option
                            value="1"
                            selected
                    >
                        {{ __('1s') }}
                    </option>
                    <option value="2">
                        {{ __('2s') }}
                    </option>
                    <option value="3">
                        {{ __('3s') }}
                    </option>
                    <option value="4">
                        {{ __('4s') }}
                    </option>
                </x-forms.input>
            @endif
        </div>

        <div>
            <div class="mb-2 flex items-center space-x-3">
                <p class="m-0 text-2xs font-medium text-heading-foreground">
                    {{ __('Speeches') }}
                </p>
                <x-button
                        class="add-new-text rounded-md bg-green-200/80 px-1.5 py-0.5 text-3xs text-black hover:bg-green-300"
                        tag="button"
                >
                    <x-tabler-plus class="size-3" />
                    {{ __('Add New') }}
                </x-button>
            </div>

            <div class="speeches"></div>
        </div>

        <div class="flex border-t border-foreground/10 pt-8">
            <x-button
                    class="py-2.5"
                    id="generate_speech_button"
                    tag="button"
                    type="submit"
                    size="lg"
            >
                <x-tabler-plus class="size-5" />
                {{ __('Generate') }}
            </x-button>
        </div>
    </form>
</x-card>

<div id="generator_sidebar_table">
    @include('panel.user.openai.components.generator_sidebar_table')
</div>

<template id="speech-template">
    <div class="speech mb-3">
        <div class="speech-info mb-2 flex items-center justify-between gap-2">
            <div>
                <span class="data-lang mr-1 text-lg"></span>
                <span class="data-voice"></span>
            </div>
            <div class="say-as-container min-w-24 empty:hidden"></div>
            <div class="ms-auto flex items-center gap-1">
                <div
                        class="data-audio hidden items-center gap-1 [&.active]:flex"
                        data-audio
                >
                    <button type="button">
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="9"
                                height="9"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                        >
                            <path
                                    stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none"
                            ></path>
                            <path
                                    d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z"
                                    stroke-width="0"
                                    fill="currentColor"
                            ></path>
                        </svg>
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="10"
                                height="10"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                        >
                            <path
                                    stroke="none"
                                    d="M0 0h24v24H0z"
                                    fill="none"
                            ></path>
                            <path
                                    d="M9 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z"
                                    stroke-width="0"
                                    fill="currentColor"
                            ></path>
                            <path
                                    d="M17 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z"
                                    stroke-width="0"
                                    fill="currentColor"
                            ></path>
                        </svg>
                    </button>
                    <div class="audio-preview grow"></div>
                    <span>0:00</span>
                </div>
                <x-button
                        class="preview-speech size-9 group"
                        variant="ghost-shadow"
                        size="none"
                        type="button"
                        title="{{ __('Preview') }}"
                >
                    <x-tabler-volume class="size-4 group-[.loading]:hidden" />
                    <x-tabler-refresh class="size-4 lqd-icon-loader hidden animate-spin group-[.loading]:block" />
                </x-button>
                <x-button
                        class="delete-speech size-9"
                        variant="danger"
                        size="none"
                        type="button"
                        title="{{ __('Delete') }}"
                >
                    <x-tabler-x class="size-4" />
                </x-button>
            </div>
        </div>
        <x-forms.input
                class="bg-background placeholder:text-foreground/50"
                data-platform=""
                data-voice=""
                data-language=""
                data-lang=""
                data-pace=""
                data-break=""
                data-modelopenai=""
                data-voiceopenai=""
                size="lg"
                type="textarea"
                name=""
                placeholder="{{ __('Write something...') }}"
                cols="30"
                rows="3"
        ></x-forms.input>
    </div>
</template>

<template id="speech-say-as-template">
    <x-forms.input
            class="say-as"
            size="lg"
            type="select"
    >
        <option
                value="0"
                selected
        >{{ __('say-as') }}</option>
        <option value="currency">{{ __('currency') }}</option>
        <option value="telephone">{{ __('telephone') }}</option>
        <option value="verbatim">{{ __('verbatim') }}</option>
        <option value="date">{{ __('date') }}</option>
        <option value="characters">{{ __('characters') }}</option>
        <option value="cardinal">{{ __('cardinal') }}</option>
        <option value="ordinal">{{ __('ordinal') }}</option>
        <option value="fraction">{{ __('fraction') }}</option>
        <option value="bleep">{{ __('bleep') }}</option>
        <option value="unit">{{ __('unit') }}</option>
        <option value="unit">{{ __('time') }}</option>
    </x-forms.input>
</template>
