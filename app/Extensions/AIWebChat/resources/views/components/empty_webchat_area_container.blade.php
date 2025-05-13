<div class="absolute group/modal hidden z-50 [&.lqd-is-active]:block" id='modal'>
    <div class="fixed top-0 z-10 hidden w-screen h-screen modal__back bg-black/25 start-0"></div>
    <div
        class="modal__content w-[90%] sm:w-[500px] lg:w-[1000px] opacity-0 invisible fixed z-20 bg-[--tblr-body-bg] !p-5 shadow-xl rounded-2xl top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 group-[&.lqd-is-active]/modal:!opacity-100 group-[&.lqd-is-active]/modal:!visible">
        <div
            class="modal__header flex items-center justify-between h-[50px] border-solid border-[--tblr-border-color] !p-4 rounded-lg mx-[20px] my-[5px] ">
            <label class="font-semibold">{{ __('Prompt Library') }}</label>
            <div>
                <a class="font-bold cursor-default btn-primary" id="add_btn">{{ __('Add +') }}</a>
                <div id='custom__popover' class="absolute">
                    <div class="hidden custom__popover__back"></div>
                    <div
                        class="custom__popover__content left-[-400px] w-[400px] bg-[#FaFaFc] p-[15px] dark:bg-[--tblr-body-bg]">
                        <div class="absolute h-[100vh] w-[100wh] bg-black"></div>
                        <input type="text" class="form-control my-2 bg-[--tblr-body-bg]"
                            placeholder="{{ __('Add Title') }}" id="new_prompt_title">
                        <textarea class="form-control my-2 w-full bg-[--tblr-body-bg]" rows=6 placeholder="{{ __('Add custom prompt') }}"
                            id="new_prompt"></textarea>
                        <div class="p-1 modal-footer">
                            <button type="button" class="w-full btn me-auto"
                                id="btn_add_new_prompt">{{ __('Done') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="prompt_filter" class="flex flex-wrap items-center justify-between px-4 py-2 sm:flex-nowrap">
            <div class="flex flex-wrap content-center gap-2 justify-evenly sm:justify-start">
                <button
                    class="filter_btn btn active flex items-center justify-center gap-2 border-none bg-[transparent] !py-1 px-3 shadow-none hover:bg-black/5 dark:hover:bg-white/10 [&.active]:bg-[--lqd-pink]"
                    filter="all">
                    {{ __('All') }}
                </button>
                <button
                    class="filter_btn btn flex items-center justify-center gap-2 border-none bg-[transparent] !py-1 px-3 shadow-none hover:bg-black/5 dark:hover:bg-white/10 [&.active]:bg-[--lqd-pink]"
                    filter="fav">
                    {{ __('Favourites') }}
                    <label class="color-[#2B2F37] mb-0 text-[1em] opacity-30" id="fav_count">8</label>
                </button>
                <button
                    class="filter_btn btn flex items-center justify-center gap-2 border-none bg-[transparent] !py-1 px-3 shadow-none hover:bg-black/5 dark:hover:bg-white/10 [&.active]:bg-[--lqd-pink]"
                    filter="per">
                    {{ __('Personal') }}
                    <label class="color-[#2B2F37] mb-0 text-[1em] opacity-30" id="per_count">8</label>
                </button>
            </div>
            <div class="input-icon w-[100%] sm:w-full lg:w-auto">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="17" height="17"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                        <path d="M21 21l-6 -6"></path>
                    </svg>
                </span>
                <input type="search" class="form-control navbar-search-input peer dark:!bg-zinc-900 max-lg:!rounded-md"
                    id="search_str" onkeyup="searchStringChange()" placeholder="{{ __('Search') }}"
                    aria-label="{{ __('Search in website') }}">
            </div>
        </div>

        <div id="no_prompt" class="flex-colmy-2 my-[20px] flex hidden cursor-default rounded-[15px] p-[30px]"
            style="box-shadow: 0px 2px 5px 0px rgba(29,39, 59, 0.05); hover: {cursor-default hidden"
            style="box-shadow: 0px 2px 5px 0px3gb}">
            <div class="prompt_header my-[10px] flex content-center justify-between">
                <p class="my-2 text-[17px] font-semibold">{{ __('No Prompts, Please input new one') }}</p>
            </div>
        </div>

        <div id="prompts" class="mx-2 flex max-h-[550px] flex-wrap justify-between gap-y-6 overflow-scroll p-[10px]">
        </div>
    </div>
</div>

<div
    class="flex h-[82px] justify-between border-b border-l-0 border-r-0 border-t-0 border-solid border-[--tblr-border-color] p-[20px] max-sm:px-4">
    <div class="flex gap-2">
        <div class="inline-flex h-[50px] w-[50px] items-center justify-center overflow-hidden overflow-ellipsis whitespace-nowrap rounded-full border-[6px] border-solid !border-white text-[13px] font-medium text-[rgba(0,0,0,0.65)] shadow-[0_1px_2px_rgba(0,0,0,0.07)] transition-shadow group-hover:shadow-xl dark:!border-current"
            style="background: {{ $category->color }};">
            @if ($category->image != null)
                <img class="object-cover object-center w-full h-full" src="/{{ $category->image }}"
                    alt="{{ __($category->name) }}">
            @else
                <span
                    class="block w-full overflow-hidden text-center overflow-ellipsis whitespace-nowrap">{{ __($category->short_name) }}</span>
            @endif
        </div>
        <div class="flex flex-col items-start justify-center">
            @if ($category->human_name != '')
                <p class="p-0 m-0 text-sm font-semibold text-heading">
                    {{ __($category->human_name) }}</p>
            @endif
            @if ($category->role != '')
                <p class="p-0 m-0 text-sm font-normal">
                    {{ __($category->role) }}</p>
            @endif
        </div>
    </div>
    <div class="flex gap-2 grow justify-end">
        <div class="flex w-full justify-end">
            <div class="mx-2 mt-2 flex gap-3 relative w-full justify-end">
                <input class="form-control w-2/3" type="text" name="website_url" id="website_url"
                    placeholder="Input website url to analyze">
                <button class="w-fit btn flex justify-center items-center" id="btn_analyze"
                    onclick="return startNewChat({{ $category->id }}, '{{ LaravelLocalization::getCurrentLocale() }}');"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-analyze md:hidden m-0"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M20 11a8.1 8.1 0 0 0 -6.986 -6.918a8.095 8.095 0 0 0 -8.019 3.918" />
                        <path d="M4 13a8.1 8.1 0 0 0 15 3" />
                        <path d="M19 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M5 8m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                    </svg>
                    <p class="m-0 hidden md:block">{{ __('Analyze') }}</p>
                </button>
            </div>
            <label class="form-check form-switch mx-2 mt-2 hidden">
                <input class="form-check-input" type="checkbox" name="realtime" id="realtime">
                <span class="form-check-label">{{ __('Use Real-Time Data') }}</span>
            </label>
        </div>
    </div>
</div>

<div class="conversation-area flex grow flex-col justify-between overflow-y-auto max-md:max-h-[70vh] max-sm:max-h-full"
    id="chat_area_to_hide">
    <div class="relative flex flex-col grow">
        <div
            class="chats-container @if ($category->slug == 'ai_vision') mb-32 md:mb-0 relative z-10 @else h-full @endif p-8 max-md:p-4">

            @include('panel.user.openai_chat.components.webchat_area')

            @if (
                $category->slug == 'ai_vision' &&
                    ((isset($lastThreeMessage) && $lastThreeMessage->count() == 0) || !isset($lastThreeMessage)))
                <div class="flex flex-col items-center justify-center gap-y-3" id="sugg">
                    <div class="text-xs font-medium leading-relaxed text-heading">
                        {{ __('Upload an image and ask me anything') }} <span><svg xmlns="http://www.w3.org/2000/svg"
                                class="icon icon-tabler icon-tabler-chevron-down" width="15" height="15"
                                viewBox="0 0 30 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 9l6 6l6 -6" />
                            </svg></span> </div>

                    <div
                        class="flex-col items-center justify-start px-4 py-2 bg-purple-100 cursor-pointer rounded-3xl">
                        <div class="text-sm font-normal leading-tight text-black"
                            onclick="addText('{{ __('Explain an Image') }}');">{{ __('Explain an Image') }}</div>
                    </div>
                    <div class="flex-col items-center justify-start px-4 py-2 bg-purple-100 cursor-pointer rounded-3xl"
                        onclick="addText('{{ __('Summarize a book for Research') }}');">
                        <div class="text-sm font-normal leading-tight text-black">
                            {{ __('Summarize a book for Research') }}</div>
                    </div>
                    <div
                        class="flex-col items-center justify-start px-4 py-2 bg-purple-100 cursor-pointer rounded-3xl">
                        <div class="text-sm font-normal leading-tight text-black"
                            onclick="addText('{{ __('Translate a book') }}');">{{ __('Translate a book') }}</div>
                    </div>
                </div>
            @endif

        </div>

        @if (
            $category->slug == 'ai_vision' &&
                ((isset($lastThreeMessage) && $lastThreeMessage->count() == 0) || !isset($lastThreeMessage)))
            <div id="mainupscale_src" class="z-10 flex items-center justify-center px-4 mt-auto"
                ondrop="dropHandler(event, 'upscale_src');" ondragover="dragOverHandler(event);">
                <label for="upscale_src"
                    class="flex flex-col items-center justify-center w-full border-2 border-dashed rounded-lg cursor-pointer z-5 dark:hover:bg-bray-800 border-black/5 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                    <div class="flex flex-col items-center justify-center pt-4 pb-6">
                        <svg class="w-8 h-8 mb-1 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1"
                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                        </svg>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-semibold">{{ __('Drop your image here or browse') }}
                        </p>
                        <p class="text-xs text-gray-500 file-name dark:text-gray-400">
                            {{ __('(Only jpg, png, webp will be accepted)') }}</p>
                    </div>
                    <input id="upscale_src" type="file" class="hidden" accept=".png, .jpg, .jpeg"
                        onchange="handleFileSelect('upscale_src')" />
                </label>
            </div>
        @endif
    </div>
    {{-- @endif --}}

</div>


<template id="unselected_prompt">
    <div
        class="prompt w-[48.5%] cursor-pointer rounded-2xl border-none shadow-[0_2px_5px_rgba(29,39,59,0.05)] transition-all duration-300 translate-x-0 translate-y-0 bg- hover:-translate-y-1 hover:shadow-lg dark:bg-white/[1%]">
        <div class="flex justify-between gap-2 card-body">
            <div class="grow">
                <div class="flex items-center justify-between prompt_header">
                    <p class="prompt_title my-2 text-[17px] font-semibold"></p>
                </div>
                <div class="prompt-body">
                    <p class="prompt_text text-[13px] font-normal"></p>
                </div>
            </div>
            <div
                class="favbtn group/favbtn flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[--tblr-body-bg] shadow-md transition-all hover:-translate-y-1 hover:scale-105 [&.active]:bg-[--lqd-pink] dark:[&.active]:text-black">
                <svg class="group-[&.active]/favbtn:hidden" width="15" height="13" viewBox="0 0 15 13"
                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M7.5 13L6.4125 12.079C5.15 11.0045 4.10625 10.0777 3.28125 9.29836C2.45625 8.51907 1.8 7.81948 1.3125 7.19959C0.825 6.5797 0.484375 6.00999 0.290625 5.49046C0.096875 4.97094 0 4.4396 0 3.89646C0 2.78656 0.39375 1.85967 1.18125 1.1158C1.96875 0.371935 2.95 0 4.125 0C4.775 0 5.39375 0.129882 5.98125 0.389646C6.56875 0.64941 7.075 1.01544 7.5 1.48774C7.925 1.01544 8.43125 0.64941 9.01875 0.389646C9.60625 0.129882 10.225 0 10.875 0C12.05 0 13.0313 0.371935 13.8188 1.1158C14.6063 1.85967 15 2.78656 15 3.89646C15 4.4396 14.9031 4.97094 14.7094 5.49046C14.5156 6.00999 14.175 6.5797 13.6875 7.19959C13.2 7.81948 12.5437 8.51907 11.7188 9.29836C10.8938 10.0777 9.85 11.0045 8.5875 12.079L7.5 13ZM7.5 11.0872C8.7 10.0718 9.6875 9.20095 10.4625 8.4748C11.2375 7.74864 11.85 7.11694 12.3 6.5797C12.75 6.04246 13.0625 5.56426 13.2375 5.1451C13.4125 4.72593 13.5 4.30972 13.5 3.89646C13.5 3.18801 13.25 2.59764 12.75 2.12534C12.25 1.65304 11.625 1.41689 10.875 1.41689C10.2875 1.41689 9.74375 1.57334 9.24375 1.88624C8.74375 2.19914 8.4 2.59764 8.2125 3.08174H6.7875C6.6 2.59764 6.25625 2.19914 5.75625 1.88624C5.25625 1.57334 4.7125 1.41689 4.125 1.41689C3.375 1.41689 2.75 1.65304 2.25 2.12534C1.75 2.59764 1.5 3.18801 1.5 3.89646C1.5 4.30972 1.5875 4.72593 1.7625 5.1451C1.9375 5.56426 2.25 6.04246 2.7 6.5797C3.15 7.11694 3.7625 7.74864 4.5375 8.4748C5.3125 9.20095 6.3 10.0718 7.5 11.0872Z" />
                </svg>
                <svg class="hidden group-[&.active]/favbtn:block" width="15" height="13" viewBox="0 0 14 13"
                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.64744 12.3854L5.72321 11.5568C4.65025 10.59 3.7632 9.75611 3.06206 9.05497C2.36092 8.35383 1.8032 7.72439 1.38889 7.16667C0.974578 6.60894 0.685092 6.09637 0.52043 5.62894C0.355768 5.16151 0.273438 4.68346 0.273438 4.19479C0.273438 3.19619 0.608073 2.36226 1.27734 1.69299C1.94661 1.02372 2.78055 0.689087 3.77914 0.689087C4.33155 0.689087 4.85741 0.805944 5.3567 1.03966C5.856 1.27337 6.28625 1.60269 6.64744 2.02763C7.00863 1.60269 7.43888 1.27337 7.93818 1.03966C8.43747 0.805944 8.96333 0.689087 9.51574 0.689087C10.5143 0.689087 11.3483 1.02372 12.0175 1.69299C12.6868 2.36226 13.0214 3.19619 13.0214 4.19479C13.0214 4.68346 12.9391 5.16151 12.7745 5.62894C12.6098 6.09637 12.3203 6.60894 11.906 7.16667C11.4917 7.72439 10.934 8.35383 10.2328 9.05497C9.53168 9.75611 8.64463 10.59 7.57167 11.5568L6.64744 12.3854Z" />
                </svg>
            </div>
        </div>
    </div>
</template>

<template id="selected_prompt">
    <div
        class="prompt w-[48.5%] cursor-pointer rounded-2xl border-none shadow-[0_2px_5px_rgba(29,39,59,0.05)] transition-all duration-300 translate-x-0 translate-y-0 bg- hover:-translate-y-1 hover:shadow-lg dark:bg-white/[1%]">
        <div class="flex justify-between gap-2 card-body">
            <div class="grow">
                <div class="flex items-center justify-between prompt_header">
                    <p class="prompt_title my-2 text-[17px] font-semibold"></p>
                </div>
                <div class="prompt-body">
                    <p class="prompt_text text-[13px] font-normal"></p>
                </div>
            </div>
            <div
                class="favbtn group/favbtn flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[--tblr-body-bg] shadow-md transition-all hover:-translate-y-1 hover:scale-105 [&.active]:bg-[--lqd-pink] dark:[&.active]:text-black">
                <svg class="group-[&.active]/favbtn:hidden" width="15" height="13" viewBox="0 0 15 13"
                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M7.5 13L6.4125 12.079C5.15 11.0045 4.10625 10.0777 3.28125 9.29836C2.45625 8.51907 1.8 7.81948 1.3125 7.19959C0.825 6.5797 0.484375 6.00999 0.290625 5.49046C0.096875 4.97094 0 4.4396 0 3.89646C0 2.78656 0.39375 1.85967 1.18125 1.1158C1.96875 0.371935 2.95 0 4.125 0C4.775 0 5.39375 0.129882 5.98125 0.389646C6.56875 0.64941 7.075 1.01544 7.5 1.48774C7.925 1.01544 8.43125 0.64941 9.01875 0.389646C9.60625 0.129882 10.225 0 10.875 0C12.05 0 13.0313 0.371935 13.8188 1.1158C14.6063 1.85967 15 2.78656 15 3.89646C15 4.4396 14.9031 4.97094 14.7094 5.49046C14.5156 6.00999 14.175 6.5797 13.6875 7.19959C13.2 7.81948 12.5437 8.51907 11.7188 9.29836C10.8938 10.0777 9.85 11.0045 8.5875 12.079L7.5 13ZM7.5 11.0872C8.7 10.0718 9.6875 9.20095 10.4625 8.4748C11.2375 7.74864 11.85 7.11694 12.3 6.5797C12.75 6.04246 13.0625 5.56426 13.2375 5.1451C13.4125 4.72593 13.5 4.30972 13.5 3.89646C13.5 3.18801 13.25 2.59764 12.75 2.12534C12.25 1.65304 11.625 1.41689 10.875 1.41689C10.2875 1.41689 9.74375 1.57334 9.24375 1.88624C8.74375 2.19914 8.4 2.59764 8.2125 3.08174H6.7875C6.6 2.59764 6.25625 2.19914 5.75625 1.88624C5.25625 1.57334 4.7125 1.41689 4.125 1.41689C3.375 1.41689 2.75 1.65304 2.25 2.12534C1.75 2.59764 1.5 3.18801 1.5 3.89646C1.5 4.30972 1.5875 4.72593 1.7625 5.1451C1.9375 5.56426 2.25 6.04246 2.7 6.5797C3.15 7.11694 3.7625 7.74864 4.5375 8.4748C5.3125 9.20095 6.3 10.0718 7.5 11.0872Z" />
                </svg>
                <svg class="hidden group-[&.active]/favbtn:block" width="15" height="13" viewBox="0 0 14 13"
                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.64744 12.3854L5.72321 11.5568C4.65025 10.59 3.7632 9.75611 3.06206 9.05497C2.36092 8.35383 1.8032 7.72439 1.38889 7.16667C0.974578 6.60894 0.685092 6.09637 0.52043 5.62894C0.355768 5.16151 0.273438 4.68346 0.273438 4.19479C0.273438 3.19619 0.608073 2.36226 1.27734 1.69299C1.94661 1.02372 2.78055 0.689087 3.77914 0.689087C4.33155 0.689087 4.85741 0.805944 5.3567 1.03966C5.856 1.27337 6.28625 1.60269 6.64744 2.02763C7.00863 1.60269 7.43888 1.27337 7.93818 1.03966C8.43747 0.805944 8.96333 0.689087 9.51574 0.689087C10.5143 0.689087 11.3483 1.02372 12.0175 1.69299C12.6868 2.36226 13.0214 3.19619 13.0214 4.19479C13.0214 4.68346 12.9391 5.16151 12.7745 5.62894C12.6098 6.09637 12.3203 6.60894 11.906 7.16667C11.4917 7.72439 10.934 8.35383 10.2328 9.05497C9.53168 9.75611 8.64463 10.59 7.57167 11.5568L6.64744 12.3854Z" />
                </svg>
            </div>
        </div>
    </div>
</template>

<template id="prompt_image">
    <div class="relative m-2 rounded-[10px]">
        <button onclick="document.getElementById('mainupscale_src').style.display = 'block';"
            class="prompt_image_close absolute right-[2px] top-[2px] flex h-[20px] w-[20px] items-center justify-center rounded-[10px] border-none outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>

        </button>
        <img class="m-0 rounded-[10px]" style="width: 80px;height:60px"
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAUCAYAAADskT9PAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAUmSURBVEhLtVbbaxxlFP/N7M7Mzu7sfdfNJrWlF4ut1koQqU2xtlKpoEJfCuKD/gW+SPFRBa2iKVLUJ/FFUKHggxYRLN6gIm1TjWlNY5LaJk3abLLZzV6yt7l5zje7mwtWQelvmd35vvnmnN+5r/TMiffcxcoyOnDpw5Dooyl+cX8n4LguInoA0sBrb7l779mKbCQMnyyj1jKh+X1oWTZOD1+64ySkg28MupbrgO49SHTRvUS/qr+tnB46TucAvUjk3O4L/w2sRiJDpcffPOEu5ctYrtSE1RwCmR4zO/aI4vNB01WkeuKCBCsPb+yFEgwIYqvBK9NxRPjYAJbBqlSZ1a2Gty6MXYX0xNvvuqNDE1jIFXH0wB5oPhnF5RoyiSimcov47pffEQrp6N+/C1bLgtVoYuvTB+HXmIADm3R0xOtE9tFUCvlWCxXTRDagg3Wfmc8RCbl9yoOf4j/6yRcegbFf/0SpVBPWd8TJbAKDrNBUGbsH7usS2Hx4P/wBDTZZG6UwWXSmYllo2DZZ7nmPFes+v5Amro68Nvj9sVNfeQSuDF+He20UbrMJSVXbR9iRgK3qaMQy6B/YAZMI2M0WNj9JBDQNJbOFl7fvgEPWNRwbJpHoQJd9+PDaVfhIcdeYVegQEH5xJB9i+SnsOnIUPWEVGzJJZONBZA0/AoUZ7NyyCQnDQIoqJR2NIEXK40R0uxHGw4kE0jdvYkuhiEN3ZXAomcKhRBL7KBT2uhyhnKPE5r2VfS8wTNCx0Lw1BbdRR2NqHM5SAc5yBRa5uS8Vx7Z0EhvjMfQRAVWSEaJ4z9br4nWOfSQUwsL0NMrFIpbyebG/Gqz83KiDr8/5YFmksO0UQUC2LeQ29WN0eBizUhDXE9txET0YQhb1vp04/dN5EW92Z75SxXRtGTOkvEAhYITJWomTTFG8zG+Xb9dOuuH82paOI5ExENLFhoBHgKxvRDYh99hJzD30Ku5PKnCP0fqlDVgwVRiqgkuzt1Cs1ZArl6GQMo4rlysjnEwimskgSKHRSXmQPMLoRp5uqlUbtfoMmTSLxYIlypTRbXN1y8ULj2xEr97CmXdoo3EvQos3iV2eDiswKcMncnmqPOpebdFpyoNnz/0sjOFQ7SFPnKcQ+Em6S2sODZ/kphWKKjBiXoLzupMewgMuxdSwl3Dqo1dw8v3XcUP2Y+DzSzjwwxxZ304TEuqnHtG1isB7XN8aXToFOUi/AdrT6WIvdM7KJGPpxyJqQ2WUL5RQPVuicHlPO9LhUB4otRy0Zh5NWo/UShgjl3PC3Q5sdZbcng0EqPFYuFKuUGmaKFAjWn2ZpgPjwTC03QaM/giCe6PUMj0XeJ3w4iQixDhmBDE+MycSid0kONI5Rdf+thHNNxr4cu8+IeifcGxkmHKpRfFe8d+aPsDbczSRm/4o6oEUyloCSqIXTjyLVryHStThY2uwIgrY+fxxDH72DeZmb2BychIT439gZHwa41Nz4jlPGLJK3K+HSEKZDhSg4uRzh/Hx2cvYcXeaOpkLm8pq8NshGNUFcXg9OiJ/++BFLFbq6Mmm2ztrsTZz1sILwYUJlEhA1bTFfwHuYCyc+3mA1qrk0jB6YGUYPUXDiFxYo9Z7pLcPJguiy6JKWQ+NWvL3C/NoUqvuVA9D4WH0KQ2jg8cH3eJCCcs0jLiXrIawkL5UyoFMX4oi4VDDtBCj1qzQhOQ84QH0b+AqWT+MGPMjYxB/yao0hHj23w5cs6y8Aybxf/+Q8OuxiIG/ANTLQ4Xeth7OAAAAAElFTkSuQmCC" />
    </div>
</template>

<template id="prompt_pdf">
    <div class="relative m-2 flex h-[80px] items-end rounded-[10px]">
        <button onclick="document.getElementById('mainupscale_src').style.display = 'block';"
            class="prompt_pdf_close absolute right-[2px] top-[2px] flex h-[20px] w-[20px] items-center justify-center rounded-[10px] border-none outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>

        </button>
        <label></label>
    </div>
</template>


<template id="prompt_image_add_btn">
    <div class="promt_image_btn m-2 h-[60px] w-[60px] rounded-[10px]">
        <button
            class="h-full w-full rounded-[10px] border-none outline-none hover:bg-gray-300 focus:border-none focus:ring-0">+</button>
    </div>
</template>

<template id="chat_pdf">
    <div class="mb-2 mr-[30px] flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M23.7762 0H5.11921C4.59978 0 4.17871 0.421071 4.17871 1.23814V35.3571C4.17871 35.5789 4.59978 36 5.11921 36H30.8811C31.4005 36 31.8216 35.5789 31.8216 35.3571V8.343C31.8216 7.89557 31.7618 7.75157 31.6564 7.6455L24.1761 0.165214C24.07 0.0597857 23.926 0 23.7762 0Z"
                fill="#E9E9E0" />
            <path d="M24.1074 0.0970459V7.71426H31.7246L24.1074 0.0970459Z" fill="#D9D7CA" />
            <path
                d="M12.5445 21.4226C12.3208 21.4226 12.1061 21.35 11.9229 21.2131C11.2537 20.711 11.1637 20.1523 11.2061 19.7718C11.3231 18.7252 12.6172 17.6298 15.0536 16.5138C16.0205 14.3949 16.9404 11.7843 17.4888 9.60306C16.8472 8.20677 16.2236 6.3952 16.6781 5.33256C16.8375 4.96035 17.0362 4.67492 17.4071 4.55149C17.5537 4.50263 17.924 4.44092 18.0603 4.44092C18.3843 4.44092 18.669 4.85813 18.8709 5.11527C19.0605 5.35699 19.4906 5.86935 18.6311 9.48799C19.4977 11.2777 20.7255 13.1008 21.902 14.3493C22.7448 14.1969 23.4699 14.1191 24.0607 14.1191C25.0674 14.1191 25.6775 14.3538 25.9263 14.8372C26.132 15.2371 26.0478 15.7044 25.6755 16.2258C25.3175 16.7266 24.8238 16.9914 24.2484 16.9914C23.4667 16.9914 22.5564 16.4977 21.5413 15.5225C19.7175 15.9037 17.5878 16.5838 15.8662 17.3366C15.3288 18.4771 14.8138 19.3957 14.3343 20.0694C13.6753 20.9919 13.107 21.4226 12.5445 21.4226ZM14.2558 18.1273C12.882 18.8994 12.3221 19.5339 12.2816 19.8913C12.2752 19.9505 12.2578 20.1061 12.5587 20.3362C12.6545 20.306 13.2138 20.0508 14.2558 18.1273ZM23.0225 15.2718C23.5464 15.6748 23.6743 15.8786 24.017 15.8786C24.1674 15.8786 24.5962 15.8722 24.7948 15.5951C24.8906 15.4608 24.9279 15.3746 24.9427 15.3283C24.8636 15.2866 24.7588 15.2017 24.1873 15.2017C23.8627 15.2023 23.4545 15.2165 23.0225 15.2718ZM18.2203 11.0405C17.7607 12.6309 17.1538 14.348 16.5013 15.9031C17.8449 15.3817 19.3055 14.9266 20.6773 14.6045C19.8095 13.5965 18.9423 12.3378 18.2203 11.0405ZM17.8301 5.60063C17.7671 5.62185 16.9751 6.73013 17.8918 7.66806C18.5019 6.30842 17.8578 5.59163 17.8301 5.60063Z"
                fill="#CC4B4C" />
            <path
                d="M30.8811 36H5.11921C4.59978 36 4.17871 35.5789 4.17871 35.0595V25.0714H31.8216V35.0595C31.8216 35.5789 31.4005 36 30.8811 36Z"
                fill="#CC4B4C" />
            <path
                d="M11.176 34.0714H10.1211V27.594H11.9841C12.2592 27.594 12.5318 27.6377 12.8012 27.7258C13.0705 27.8139 13.3122 27.9456 13.5263 28.1211C13.7404 28.2966 13.9133 28.5094 14.0451 28.7582C14.1769 29.007 14.2431 29.2866 14.2431 29.5978C14.2431 29.9263 14.1872 30.2233 14.076 30.4901C13.9647 30.7569 13.8092 30.9812 13.6099 31.1625C13.4106 31.3438 13.1702 31.4846 12.8892 31.5842C12.6083 31.6839 12.2972 31.7334 11.9577 31.7334H11.1754L11.176 34.0714ZM11.176 28.3937V30.96H12.1429C12.2715 30.96 12.3987 30.9381 12.5254 30.8938C12.6514 30.8501 12.7671 30.7781 12.8725 30.6784C12.978 30.5788 13.0628 30.4399 13.1271 30.2612C13.1914 30.0825 13.2235 29.8614 13.2235 29.5978C13.2235 29.4924 13.2087 29.3702 13.1798 29.2333C13.1502 29.0957 13.0905 28.9639 12.9998 28.8379C12.9085 28.7119 12.7812 28.6065 12.6173 28.5216C12.4534 28.4368 12.2361 28.3944 11.9667 28.3944L11.176 28.3937Z"
                fill="white" />
            <path
                d="M20.7121 30.6527C20.7121 31.1856 20.6549 31.6414 20.5404 32.0194C20.426 32.3974 20.2814 32.7137 20.1052 32.9689C19.9291 33.2241 19.7317 33.4247 19.5119 33.5713C19.292 33.7179 19.0799 33.8271 18.8748 33.9011C18.6697 33.9744 18.482 34.0213 18.3123 34.0419C18.1426 34.0611 18.0166 34.0714 17.9343 34.0714H15.4824V27.594H17.4335C17.9786 27.594 18.4576 27.6808 18.8703 27.8531C19.283 28.0254 19.6263 28.2561 19.8989 28.5429C20.1714 28.8296 20.3746 29.1568 20.5096 29.5226C20.6446 29.889 20.7121 30.2657 20.7121 30.6527ZM17.5833 33.2981C18.2981 33.2981 18.8137 33.0699 19.13 32.6128C19.4463 32.1557 19.6044 31.4936 19.6044 30.6264C19.6044 30.357 19.5723 30.0902 19.508 29.8266C19.4431 29.5631 19.319 29.3246 19.1345 29.1105C18.95 28.8964 18.6993 28.7235 18.383 28.5917C18.0667 28.4599 17.6566 28.3937 17.1526 28.3937H16.5374V33.2981H17.5833Z"
                fill="white" />
            <path d="M23.3135 28.3937V30.4329H26.0206V31.1535H23.3135V34.0714H22.2412V27.594H26.2925V28.3937H23.3135Z"
                fill="white" />
        </svg>
    </div>
    <div class="mb-2 mr-[30px] flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
        <a class="flex pdfpath" href="" target="_blank">
            <label class="pdfname"></label>
            <svg width="17" height="18" viewBox="0 0 17 18" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_3243_893" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                    width="17" height="18">
                    <rect y="0.43103" width="17" height="17" fill="#D9D9D9" />
                </mask>
                <g mask="url(#mask0_3243_893)">
                    <path
                        d="M4.45937 12.9289L3.71973 12.1892L10.69 5.21212H4.35314V4.14966H12.4989V12.2955H11.4365V5.95858L4.45937 12.9289Z"
                        fill="#1C1B1F" />
                </g>
            </svg>
        </a>
    </div>
</template>
