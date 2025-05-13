<div>
    <li class="flex justify-between border border-green p-2 mb-2 rounded">
        <p class="{{ $item['permission'] ? '' : 'text-red-600' }} text-">@lang($item['label'])</p>
        <x-updater-permission :permission="$item['permission']"/>
    </li>
</div>
