@extends('panel.layout.settings', ['layout' => 'fullwidth'])
@section('title', __('Add or Edit Page'))

@section('settings')
    <form
        class="flex flex-col gap-5 [&_.tox]:bg-input-background"
        id="page_form"
        onsubmit="return pageSave({{ $page != null ? $page->id : null }});"
        action=""
        enctype="multipart/form-data"
    >
        <x-forms.input
            id="status"
            name="status"
            type="checkbox"
            label="{{ __('Page Status') }}"
            tooltip="{{ __('You can disable or enable this page. When this option is disabled, the page cannot be accessible to users.') }}"
            :checked="$page != null && $page->status"
            switcher
        />
        <x-forms.input
            id="titlebar_status"
            name="titlebar_status"
            type="checkbox"
            label="{{ __('Titlebar Status') }}"
            :checked="$page != null && $page->titlebar_status"
            switcher
        />

        <x-forms.input
            id="show_on_footer"
            name="show_on_footer"
            type="checkbox"
            label="{{ __('Show page on footer') }}"
            :checked="$page != null && $page->show_on_footer"
            switcher
        />

        <x-forms.input
            id="title"
            size="lg"
            name="title"
            value="{{ $page != null ? $page->title : null }}"
            label="{{ __('Page Title') }}"
            tooltip="{{ __('Add a page title. Example: Privacy Policy.') }}"
        />

        <x-forms.input
            id="slug"
            name="slug"
            size="lg"
            value="{{ $page != null ? $page->slug : null }}"
            label="{{ __('Slug') }}"
            tooltip="{{ __('Add Slug for SEO. Example: privaciy-policy') }}"
        />

        <x-forms.input
            id="content"
            name="content"
            size="lg"
            type="textarea"
            label="{{ __('Content') }}"
            tooltip="{{ __('A short description of what this chat template can help with.') }}"
        >{{ $page != null ? $page->content : null }}</x-forms.input>

        <x-button
            id="page_button"
            size="lg"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/page.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/tinymce-theme-handler.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        (() => {
            const options = {
                selector: '#content',
                plugins: ['quickbars', 'link', 'image', 'lists', 'code', 'table'],
                toolbar: 'undo redo | blocks | bold italic mark underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | lists | indent outdent | image | code',
                quickbars_insert_toolbar: false,
                statusbar: false,
                block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Liquid Blocks=LiquidBlocks; Highlight=highlight; Number block=numBlock; Leading=leading; Info box=infoBox;',
                content_css: `${window.liquid.assetsPath}/css/tinymce-theme.css`,
                formats: {
                    highlight: {
                        title: 'Highlight',
                        inline: 'span',
                        classes: 'highlight',
                    },
                    leading: {
                        title: 'Leading',
                        block: 'p',
                        classes: 'leading',
                    },
                    numBlock: {
                        title: 'Number block',
                        inline: 'span',
                        classes: 'num-block',
                    },
                    infoBox: {
                        title: 'Info box',
                        inline: 'span',
                        classes: 'info-box',
                    },
                },
                style_formats: [{
                    title: 'Liquid Blocks'
                }, {
                    name: 'highlight',
                    format: 'highlight',
                }, {
                    name: 'numBlock',
                    format: 'numBlock'
                }, {
                    name: 'leading',
                    format: 'leading',
                }, {
                    name: 'infoBox',
                    format: 'infoBox',
                }],
                content_style: `
				*, *:before, *:after {
					box-sizing: border-box;
				}

				hr {
					border-color: rgb(0 0 0 / 10%);
					padding: 1em 0;
				}

				.highlight {
					font-size: 12px;
					line-height: 1.5em;
					font-weight: 500;
					display: inline-block;
					border-radius: 6px;
					padding: 2px 14px 2px 14px;
					background: linear-gradient(90deg, #ECEAF8 0%, #E9C4E6 35%, #D2CDE2 70%, #EAEDFB 100%);
				}

				.leading {
					font-size: 20px;
					font-weight: 600;
					line-height: 1.2em;
					margin-bottom: 2.5em;
				}

				.num-block {
					display: inline-flex;
					min-width: 1.9412em;
					min-height: 1.9412em;
					padding: 0.25em 0.5em;
					align-items: center;
					justify-content: center;
					font-size: 17px;
					font-weight: 700;
					border-radius: 10px;
					background-color: #F3E3F8;
				}

				.info-box {
					display: inline-block;
					font-size: 14px;
					font-weight: 500;
					background-color: rgb(0 0 0 / 10%);
					padding: 4px 17px 4px 17px;
					border-radius: 11px;
				}
				`,
                // The following option is used to append style formats rather than overwrite the default style formats.
                style_formats_merge: true,
                setup: function(editor) {
                    liquidTinyMCEThemeHandlerInit(editor);
                }
            };
            tinymce.init(options);
        })();
    </script>
@endpush
