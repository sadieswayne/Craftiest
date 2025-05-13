<div class="mx-auto">
    <p class="">@lang('Welcome, ') {{ $user->name }}</p>
    <small class="mb-4">
        @lang('You are not using the latest version of the system. You can upgrade to the latest version by clicking the button below.')
    </small>

    <x-card
        class="text-center shadow-xl shadow-black/5 mb-4 mt-4"
        variant="shadow"
    >
        <ul>
            @foreach($data['updater']['list'] as $item)
                @if($item['permission'] === false)
                    @php($permission = $item['permission'])
                        @endif
                        <x-updater-li
                            :item="$item"
                        />
                        @endforeach
        </ul>

        @if($permission === false)
            <x-alert variant="danger" class="mt-2 mb-2">
                <p>
                    @lang("You must have the necessary hardware to perform the update.")
                </p>
            </x-alert>
        @endif
    </x-card>

    <form method="post" action="{{  route('updater.backup') }}">
        @csrf
        <x-updater-button
            :permission="$permission"
            :text="__('Backup current version')"
{{--            :text="$permission ? __('Upgrade ') . $data['version'].'\'version' : __('Upgrade ') . $data['version'].'\'version'"--}}
        />
    </form>
</div>
