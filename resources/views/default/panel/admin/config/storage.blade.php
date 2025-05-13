@extends('panel.layout.settings')
@section('title', __('Storage Settings'))
@section('titlebar_actions', '')
@section('additional_css')

@endsection

@section('settings')
    <form action="{{route("dashboard.admin.config.storage.store")}}" method="POST">
        @csrf
        <h3 class="mb-[25px] text-[20px]">{{ __('Image Storage Settings') }}</h3>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Default Storage') }}</label>
                    <select
                            class="form-select"
                            id="ai_image_storage"
                            name="ai_image_storage"
                    >
                        <option value="{{\App\Enums\Storage::PUBLIC->value}}"
                                {{ $settings_two->ai_image_storage == \App\Enums\Storage::PUBLIC->value ? 'selected' : '' }}>
                            {{ \App\Enums\Storage::PUBLIC->label()  }}
                        </option>
                        <option value="{{\App\Enums\Storage::S3->value}}"
                                {{ $settings_two->ai_image_storage == \App\Enums\Storage::S3->value ? 'selected' : '' }}>
                            {{ \App\Enums\Storage::S3->label()  }}
                        </option>
                        @if($cloudflare)
                            <option value="{{\App\Enums\Storage::R2->value}}"
                                    {{ $settings_two->ai_image_storage == \App\Enums\Storage::R2->value ? 'selected' : '' }}>
                                {{ \App\Enums\Storage::R2->label()  }}
                            </option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-12">
                <button
                        class="btn btn-primary w-full"
                        type="submit"
                >
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@push('script')

@endpush
