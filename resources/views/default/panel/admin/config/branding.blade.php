@extends('panel.layout.settings')
@section('title', __('Brand Identity'))
@section('titlebar_actions', '')
@section('additional_css')
    <link
        rel="stylesheet"
        href="https://foliotek.github.io/Croppie/croppie.css"
    />
    <style>
        #upload-demo {
            width: 250px;
            height: 250px;
            padding-bottom: 25px;
            margin: 0 auto;
        }
    </style>
@endsection

@section('settings')

    <h3 class="mb-[25px] text-[20px]">{{ __('Global Settings') }}</h3>
    <form id="submitForm" action="{{route("dashboard.admin.config.branding.store")}}" method="POST"
          enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site Name') }}</label>
                    <input
                        class="form-control"
                        id="site_name"
                        type="text"
                        name="site_name"
                        value="{{ $setting->site_name }}"
                    >
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site URL') }}</label>
                    <input
                        class="form-control"
                        id="site_url"
                        type="text"
                        name="site_url"
                        value="{{ $setting->site_url }}"
                    >
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Site Email') }}</label>
                    <input
                        class="form-control"
                        id="site_email"
                        type="text"
                        name="site_email"
                        value="{{ $setting->site_email }}"
                    >
                </div>
            </div>
        </div>

        <h3 class="mb-[25px] text-[20px]">{{ __('Logo Settings') }}</h3>

        @csrf
        <div class="row mb-4">
            <div class="col-md-12 mb-3">
                <div class="mb-4">
                    <label class="form-label">{{ __('Site Favicon') }}</label>
                    <input
                        class="form-control"
                        id="favicon"
                        type="file"
                        name="favicon"
                    >
                </div>
                <x-alert class="!mt-2">
                    <p>
                        {{ __('If you will use SVG, you do not need the Retina (2x) option.') }}
                    </p>
                </x-alert>
            </div>

            <div class="col-md-6">
                <h4 class="mb-3">{{ __('Default Logos') }}</h4>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo"
                        data-id="logo"
                        type="file"
                        name="logo"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo (Dark)') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_dark"
                        data-id="logo_dark"
                        type="file"
                        name="logo_dark"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo Sticky') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_sticky"
                        data-id="logo_sticky"
                        type="file"
                        name="logo_sticky"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_dashboard"
                        data-id="logo_dashboard"
                        type="file"
                        name="logo_dashboard"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo (Dark)') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_dashboard_dark"
                        data-id="logo_dashboard_dark"
                        type="file"
                        name="logo_dashboard_dark"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo Collapsed') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_collapsed"
                        data-id="logo_collapsed"
                        type="file"
                        name="logo_collapsed"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo Collapsed (Dark)') }}</label>
                    <input
                        class="form-control item-img"
                        id="logo_collapsed_dark"
                        data-id="logo_collapsed_dark"
                        type="file"
                        name="logo_collapsed_dark"
                    >
                </div>

            </div>
            <div class="col-md-6">
                <h4 class="mb-3">{{ __('Retina Logos (2x) - Optional') }}</h4>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_2x"
                        data-id="logo_2x"
                        type="file"
                        name="logo_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo (Dark)') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_dark_2x"
                        data-id="logo_dark_2x"
                        type="file"
                        name="logo_dark_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Site Logo Sticky') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_sticky_2x"
                        data-id="logo_sticky_2x"
                        type="file"
                        name="logo_sticky_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_dashboard_2x"
                        data-id="logo_dashboard_2x"
                        type="file"
                        name="logo_dashboard_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo (Dark)') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_dashboard_dark_2x"
                        data-id="logo_dashboard_dark_2x"
                        type="file"
                        name="logo_dashboard_dark_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo Collapsed') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_collapsed_2x"
                        data-id="logo_collapsed_2x"
                        type="file"
                        name="logo_collapsed_2x"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Dashboard Logo Collapsed (Dark)') }}</label>
                    <input
                        class="form-control item-img-x2"
                        id="logo_collapsed_dark_2x"
                        data-id="logo_collapsed_dark_2x"
                        type="file"
                        name="logo_collapsed_dark_2x"
                    >
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mt-4 mx-auto">
                    <button
                        class="btn btn-primary w-full"
                        type="submit"
                    >
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div
        class="modal fade"
        id="cropImagePop"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button
                        class="close"
                        data-bs-dismiss="modal"
                        type="button"
                        aria-label="Close"
                    ><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div
                        class="center-block"
                        id="upload-demo"
                    ></div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-default"
                        data-bs-dismiss="modal"
                        type="button"
                    >{{ __('Cancel and upload the image without cropping') }}</button>
                    <button
                        class="btn btn-primary"
                        id="cropImageBtn"
                        type="button"
                    >{{ __('Crop') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js?v=' . time()) }} }}"></script>
    <script
        src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"
        type="text/javascript"
        charset="utf-8"
    ></script>
    <script src="{{ custom_theme_url('https://foliotek.github.io/Croppie/croppie.js') }}"></script>

    <style
        type="text/css"
        media="screen"
    >
        .ace_editor {
            min-height: 200px;
        }
    </style>
    <script>
        var dashboard_code_before_head = ace.edit("dashboard_code_before_head");
        dashboard_code_before_head.session.setMode("ace/mode/html");

        var dashboard_code_before_body = ace.edit("dashboard_code_before_body");
        dashboard_code_before_body.session.setMode("ace/mode/html");
    </script>
    <script>
        var $uploadCrop, tempFilename, rawImg, imageId;
        var viewportWidth = 160; // Default width
        var viewportHeight = 70; // Default height
        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.upload-demo').addClass('ready');
                    $('#cropImagePop').modal('show');
                    rawImg = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                swal("Sorry - you're browser doesn't support the FileReader API");
            }
        }

        $uploadCrop = $('#upload-demo').croppie({
            viewport: {
                width: viewportWidth,
                height: viewportHeight,
            },
            enforceBoundary: false,
            enableExif: true
        });
        $('#cropImagePop').on('shown.bs.modal', function () {
            $uploadCrop.croppie('bind', {
                url: rawImg
            }).then(function () {
                console.log('jQuery bind complete');
            });
        });
        $('.item-img, .item-img-x2').on('change', function () {
            if ($(this).hasClass('item-img-x2')) {
                viewportWidth = 320;
                viewportHeight = 140;
                $uploadCrop.croppie('destroy'); // Destroy the existing croppie instance
                $uploadCrop = $('#upload-demo').croppie({ // Recreate the croppie instance with new dimensions
                    viewport: {
                        width: viewportWidth,
                        height: viewportHeight,
                    },
                    enforceBoundary: false,
                    enableExif: true
                });
            } else {
                viewportWidth = 160;
                viewportHeight = 70;
                $uploadCrop.croppie('destroy'); // Destroy the existing croppie instance
                $uploadCrop = $('#upload-demo').croppie({ // Recreate the croppie instance with default dimensions
                    viewport: {
                        width: viewportWidth,
                        height: viewportHeight,
                    },
                    enforceBoundary: false,
                    enableExif: true
                });
            }
            imageId = $(this).data('id');
            tempFilename = $(this).val();
            $('#cancelCropBtn').data('id', imageId);
            readFile(this);
        });
        $('#cropImageBtn').on('click', function (ev) {
            $uploadCrop.croppie('result', {
                type: 'blob',
                size: {
                    width: viewportWidth,
                    height: viewportHeight
                }
            }).then(function (resp) {
                var newInput = document.createElement('input');
                newInput.type = 'file';
                newInput.className = 'form-control item-img';
                newInput.setAttribute('data-id', imageId);
                newInput.id = imageId;
                newInput.name = imageId;
                var file = new File([resp], 'cropped_image.png', {
                    type: 'image/png'
                });
                let container = new DataTransfer();
                container.items.add(file);
                newInput.files = container.files;
                $('#' + imageId).replaceWith(newInput);
                $('#cropImagePop').modal('hide');
            });
        });

        var limitCheckbox = document.getElementById('limit');
        var countField = document.getElementById('countField');
        limitCheckbox.addEventListener('change', function () {
            countField.style.display = limitCheckbox.checked ? '' : 'none';
        });

        var voice_limit_checkbox = document.getElementById('voice_limit');
        var voiceCountField = document.getElementById('voiceCountField');
        voice_limit_checkbox.addEventListener('change', function () {
            voiceCountField.style.display = voice_limit_checkbox.checked ? '' : 'none';
        });
    </script>
@endpush
