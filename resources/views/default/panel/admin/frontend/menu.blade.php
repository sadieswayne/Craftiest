@extends('panel.layout.settings')
@section('title', __('Menu Settings'))
@section('titlebar_actions')

    <x-button class="add-menu">
        <x-tabler-plus class="size-4" />
        {{ __('Add Menu') }}
    </x-button>
@endsection

@section('settings')
    <form
        id="settings_form"
        onsubmit="return menuSettingsSave();"
        enctype="multipart/form-data"
    >
        <div class="row mb-4">
            <div class="col-md-12">
                <div
                    class="flex flex-col space-y-1"
                    id="menu-items"
                >
                    @foreach ($menus as $menu_item)
                        <div class="menu-item relative rounded-lg border !bg-white shadow-[0_10px_10px_rgba(0,0,0,0.06)] dark:!bg-opacity-5">
                            <h4 class="accordion-title mb-0 flex cursor-pointer items-center justify-between !gap-1 !py-1 !pe-2 !ps-4">
                                <span class="handle inline-flex size-10 cursor-move items-center justify-center rounded-md hover:bg-black hover:!bg-opacity-10 dark:hover:bg-white">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                        <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                        <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                        <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                        <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                        <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                    </svg>
                                </span>
                                <span>{{ $menu_item['title'] }}</span>
                                <small class="me-auto opacity-60">{{ $menu_item['url'] }}</small>
                                <div class="accordion-controls flex items-center">
                                    <div class="menu-delete inline-flex size-10 cursor-pointer items-center justify-center rounded-md hover:bg-red-100 hover:text-red-500">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="18"
                                            height="18"
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
                                            <path d="M4 7l16 0"></path>
                                            <path d="M10 11l0 6"></path>
                                            <path d="M14 11l0 6"></path>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                        </svg>
                                    </div>
                                </div>
                            </h4>
                            <div class="accordion-content mt-3 hidden p-3 pt-0">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Title') }}</label>
                                    <input
                                        class="form-control menu-title"
                                        type="text"
                                        name="title"
                                        value="{{ $menu_item['title'] }}"
                                        required
                                    >
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('URL') }}</label>
                                    <input
                                        class="form-control menu-url"
                                        type="text"
                                        name="url"
                                        placeholder="https://"
                                        value="{{ $menu_item['url'] }}"
                                        required
                                    >
                                </div>
                                @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('mega-menu'))
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Mega Menu') }}</label>
                                        <select
                                            class="form-control mega-menu-id"
                                            type="text"
                                            name="mega_menu_id"
                                        >
                                            <option value="">{{ __('Select Mega Menu') }}</option>
                                            @foreach ($mega_menus as $mega_menu)
                                                <option
                                                    value="{{ $mega_menu->id }}"
                                                    {{ isset($menu_item['mega_menu_id']) && $menu_item['mega_menu_id'] == $mega_menu->id ? 'selected' : '' }}
                                                >{{ $mega_menu->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <span class="form-check-label mr-2">{{ __('Open In New Tab') }}</span>
                                        <input
                                            class="form-check-input menu-target"
                                            type="checkbox"
                                            {{ $menu_item['target'] === false ? '' : 'checked' }}
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <button
            class="btn btn-primary w-full"
            id="settings_button"
            form="settings_form"
        >
            {{ __('Save') }}
        </button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>

    <script src="{{ custom_theme_url('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js') }}"></script>
    <script src="{{ custom_theme_url('https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.js') }}"></script>
    <script>
        $('#menu-items').sortable({
            handle: ".handle"
        });
    </script>

    <script>
        const new_menu_item = `
			<div class="menu-item !bg-white rounded-lg shadow-[0_10px_10px_rgba(0,0,0,0.06)] border relative dark:!bg-opacity-5">
				<h4 class="accordion-title flex items-center justify-between !gap-1 mb-0 !ps-4 !pe-2 !py-1 cursor-pointer">
					<span>Menu Item</span>
					<small class="opacity-60 me-auto">#</small>
					<div class="accordion-controls flex items-center">
						<div class="menu-delete inline-flex items-center justify-center w-10 h-10 rounded-md hover:bg-red-100 hover:text-red-500 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none"></path> <path d="M4 7l16 0"></path> <path d="M10 11l0 6"></path> <path d="M14 11l0 6"></path> <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path> <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path> </svg>
						</div>
						<span class="handle inline-flex items-center justify-center w-10 h-10 rounded-md cursor-move hover:bg-black hover:!bg-opacity-10 dark:hover:bg-white">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> </svg>
						</span>
					</div>
				</h4>
				<div class="accordion-content hidden mt-3 p-3 pt-0">
					<div class="mb-3">
						<label class="form-label">{{ __('Title') }}</label>
						<input type="text" class="form-control menu-title" name="title" value="" required>
					</div>
					<div class="mb-3">
						<label class="form-label">{{ __('URL') }}</label>
						<input type="text" class="form-control menu-url" name="title" placeholder="https://" value="" required>
					</div>
					@if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('mega-menu'))
					<div class="mb-3">
						<label class="form-label">{{ __('Mega Menu') }}</label>
						<select type="text" class="form-control mega-menu-id" name="mega_menu_id">
							<option value="">{{ __('Select Mega Menu') }}</option>
							@foreach ($mega_menus as $mega_menu)
								<option value="{{ $mega_menu->id }}" {{ isset($menu_item['mega_menu_id']) && $menu_item['mega_menu_id'] == $mega_menu->id ? 'selected' : '' }}>{{ $mega_menu->name }}</option>
							@endforeach
						</select>
					</div>
					@endif
					<div class="mb-3">
						<label class="form-check form-switch">
							<span class="form-check-label mr-2">{{ __('Open In New Tab') }}</span>
							<input class="form-check-input menu-target" type="checkbox">
						</label>
					</div>
				</div>
			</div>`;

        $('body').on('click', '.accordion-title', ev => {
            const accordionTitle = ev.currentTarget;
            accordionTitle.classList.toggle("active");
            accordionTitle.nextElementSibling.classList.toggle("hidden");
        });

        $(".add-menu").click(function() {
            $("#menu-items").append(new_menu_item);
        });

        $('body').on('input', 'input.menu-title', ev => {
            const input = ev.currentTarget;
            const value = input.value;

            input.closest('.menu-item').querySelector('.accordion-title > span').innerText = value;
        });

        $('body').on('input', 'input.menu-url', ev => {
            const input = ev.currentTarget;
            const value = input.value;

            input.closest('.menu-item').querySelector('.accordion-title > small').innerText = value;
        });

        $('body').on('click', '.menu-delete', function() {
            $(this).closest('.menu-item').remove();
        });
    </script>
@endpush
