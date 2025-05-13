@extends('panel.layout.settings', ['layout' => 'fullwidth', 'disable_tblr' => true])
@section('title', __('Add or Edit Post'))
@section('titlebar_actions')
    <div class="flex space-x-1 lg:justify-end">
        @if ($blog != null)
            <x-button
                variant="ghost-shadow"
                href="{{ url('/blog', $blog->slug) }}"
                target="_blank"
            >
                {{ __('Preview') }}
            </x-button>
        @endif
        <x-button
            id="post_button"
            type="submit"
            form="post_form"
        >
            {{ __('Save') }}
        </x-button>
    </div>
@endsection

@section('settings')
    <form
        class="[&_.tox]:bg-input-background"
        id="post_form"
        onsubmit="return blogSave({{ $blog != null ? $blog->id : null }});"
        action=""
        enctype="multipart/form-data"
    >
        <div class="flex flex-wrap justify-between">
            <div class="flex w-full flex-col gap-5 lg:w-7/12">
                <x-forms.input
                    class="blog-post-title"
                    id="title"
                    label="{{ __('Post Title') }}"
                    name="title"
                    size="lg"
                    tooltip="{{ __('Add a post title.') }}"
                    value="{{ $blog != null ? $blog->title : null }}"
                />
                <x-forms.input
                    id="content"
                    label="{{ __('Content') }}"
                    name="content"
                    type="textarea"
                    size="lg"
                    tooltip="{{ __('A short description of what this chat template can help with.') }}"
                >{{ $blog != null ? $blog->content : null }}</x-forms.input>
            </div>

            <div class="ms-auto flex w-full flex-col gap-5 lg:w-4/12">
                <div class="flex flex-col gap-2">
                    <img
                        @class([
                            'preview mb-5 rounded-lg border',
                            'hidden' => $blog == null || ($blog != null && !$blog->feature_image),
                        ])
                        alt="{{ $blog != null ? $blog->title : __('preview') }}"
                        src="{{ custom_theme_url($blog != null ? $blog->feature_image : null, true) }}"
                    >
                    <x-forms.input
                        id="feature_image"
                        type="file"
                        size="lg"
                        name="feature_image"
                        value="/{{ $blog != null ? $blog->feature_image : null }}"
                        accept=".jpg, .jpeg, .png, .webp"
                        label="{{ __('Post Image') }}"
                    />
                </div>

                <x-forms.input
                    id="status"
                    label="{{ __('Post Status') }}"
                    name="status"
                    type="select"
                    size="lg"
                >
                    <option
                        value="0"
                        @selected($blog != null && $blog->status == 0)
                    >
                        {{ __('Draft') }}
                    </option>
                    <option
                        value="1"
                        @selected($blog != null && $blog->status == 1)
                    >
                        {{ __('Publish') }}
                    </option>
                </x-forms.input>

                <x-forms.input
                    id="category"
                    type="select"
                    name="category"
                    multiple
                    size="none"
                    label="{{ __('Category') }}"
                    tooltip="{{ __('Categories of the post. Useful for filtering in the blog posts.') }}"
                    add-new
                >
                    @if ($blog != null && $blog->category)
                        @foreach (explode(',', $blog->category) as $cat)
                            <option
                                value="{{ $cat }}"
                                selected
                            >
                                {{ $cat }}
                            </option>
                        @endforeach
                    @endif
                </x-forms.input>

                <x-forms.input
                    id="tag"
                    type="select"
                    name="tag"
                    multiple
                    size="none"
                    label="{{ __('Tag') }}"
                    tooltip="{{ __('Tags of the post. Useful for filtering in the blog posts.') }}"
                    add-new
                >
                    @if ($blog != null && $blog->tag)
                        @foreach (explode(',', $blog->tag) as $tag)
                            <option
                                value="{{ $tag }}"
                                selected
                            >{{ $tag }}</option>
                        @endforeach
                    @endif
                </x-forms.input>

                <h3 class="mt-10">
                    {{ __('SEO') }}
                </h3>

                <x-forms.input
                    class="{{ setting('serper_seo_blog_title_desc', 0) == 1 ? 'input-seo' : '' }}"
                    id="seo_title"
                    name="seo_title"
                    value="{{ $blog != null ? $blog->seo_title : null }}"
                    label="{{ __('SEO Title') }}"
                    size="lg"
                    tooltip="{{ __('If you will leave empty: using the post title for the SEO') }}"
                />

                <x-forms.input
                    id="slug"
                    name="slug"
                    value="{{ $blog != null ? $blog->slug : null }}"
                    label="{{ __('Slug') }}"
                    size="lg"
                    tooltip="{{ __('Add Slug for SEO. Example: my-post') }}"
                />

                <x-forms.input
                    class="{{ setting('serper_seo_blog_title_desc', 0) == 1 ? 'input-seo' : '' }}"
                    id="seo_description"
                    name="seo_description"
                    label="{{ __('SEO Description') }}"
                    size="lg"
                    type="textarea"
                    rows="3"
                    tooltip="{{ __('A short description of what this chat template can help with for SEO') }}"
                >{{ $blog != null ? $blog->seo_description : null }}</x-forms.input>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/blog.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/tinymce-theme-handler.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const tinymceOptions = {
                selector: '#content',
                height: '610',
                plugins: 'quickbars advlist link image lists',
                //toolbar:'advlist link image lists'
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | lists | indent outdent | image',
                quickbars_insert_toolbar: false,
                statusbar: false,
                content_css: `${window.liquid.assetsPath}/css/tinymce-theme.css`,
                setup: function(editor) {
                    liquidTinyMCEThemeHandlerInit(editor);
                }
            };

            function slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-') // Replace spaces with -
                    .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                    .replace(/\-\-+/g, '-') // Replace multiple - with single -
                    .replace(/^-+/, '') // Trim - from start of text
                    .replace(/-+$/, ''); // Trim - from end of text
            }
            $('#title').on('input', function() {
                var titleValue = $(this).val();
                var slugValue = slugify(titleValue);
                $('#slug').val(slugValue);
            });

            tinymce.init(tinymceOptions);
        });
    </script>
@endpush
