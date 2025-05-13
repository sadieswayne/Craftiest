@extends('panel.layout.app')
@section('title', __('My Advertis'))

@section('content')
    <div class="py-10">
        <div class="container-xl">
            <h2>{{ __('Advertis Edit') }}</h2>
            <div class="card">
                <div id="table-default-2" class="card-table table-responsive">
                    <form class="p-4 w-1/2 m-auto" method="POST"
                        action="{{ route('dashboard.admin.advertis.update', $advertis) }}">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-col gap-4">
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Key</label>
                                <input type="text" name="key" value="{{ $advertis->key }}" disabled
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Title</label>
                                <input type="text" name="title" value="{{ old('title', $advertis->title) }}"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                        </div>
                        <div class="flex flex-col gap-4">
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Mobile Tracking Code</label>
                                <textarea name="tracking_code[mobile]" class="w-full bg-gray-400 h-40 p-2 focus:border-blue-300 rounded border-none">{{ old('tracking_code.mobile', data_get($advertis, 'tracking_code.mobile')) }}</textarea>
                            </div>
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Tablet Tracking Code</label>
                                <textarea name="tracking_code[tablet]" class="w-full bg-gray-400 h-40 p-2 focus:border-blue-300 rounded border-none">{{ old('tracking_code.tablet', data_get($advertis, 'tracking_code.tablet')) }}</textarea>
                            </div>
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Desktop Tracking Code</label>
                                <textarea name="tracking_code[desktop]" class="w-full bg-gray-400 h-40 p-2 focus:border-blue-300 rounded border-none">{{ old('tracking_code.desktop', data_get($advertis, 'tracking_code.desktop')) }}</textarea>
                            </div>
                        </div>

                        <div class="flex mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="advertis-status"
                                    @checked($advertis->status == true)>
                                <label class="form-check-label" for="advertis-status">Advertis Status</label>
                            </div>
                        </div>

                        <div class="flex">
                            <button class="p-2 rounded border-none w-full bg-blue-600 text-white text-center font-semibold">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
