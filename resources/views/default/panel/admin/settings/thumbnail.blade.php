@extends('panel.layout.settings')
@section('title', __('Thumbnail System'))
@section('titlebar_actions', '')

@section('settings')

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-check form-switch">
                    <input
                        class="form-check-input"
                        id="image_thumbnail"
                        name="image_thumbnail"
                        type="checkbox"
                        onchange="updateSetting(this.checked ? 1 : 0 );"
                        {{ setting('image_thumbnail') == 1 ? 'checked' : '' }}
                    >
                    <span class="form-check-label">{{ __('Enable Thumbnail System') }}</span>
                </label>
            </div>
        </div>
        {{-- purge thumbnails --}}
        <div class="col-md-12">
            <div class="my-3">
                <label class="form-label">{{ __('Purge Thumbnails') }}
                    <x-info-tooltip text="{{ __('Delete all images thumbnails.') }}" />
                </label>
                <button
                    class="btn btn-danger w-96"
                    onclick="purgeThumbnails();"
                >{{ __('Purge') }}</button>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function purgeThumbnails() {
            $.ajax({
                url: '{{ route('dashboard.admin.settings.thumbnail.purge') }}',
                type: 'POST',
                success: function(response) {
                    toastr.success("{{ __('Thumbnails purged successfully') }}");
                }
            });
        }

        function updateSetting(value) {
            $.ajax({
                url: '{{ route('dashboard.admin.settings.thumbnail.save') }}',
                type: 'POST',
                data: {
                    image_thumbnail: value,
                },
                success: function(response) {
                    toastr.success("{{ __('Settings updated successfully') }}");
                }
            });
        }
    </script>
@endpush
