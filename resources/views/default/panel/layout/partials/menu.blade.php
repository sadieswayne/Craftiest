@php
    $items = app(\App\Services\Common\MenuService::class)->generate();

    $isAdmin = \Auth::user()?->isAdmin();

	$user = \Auth::user();

@endphp

@foreach ($items as $key => $item)
    @if (\App\Helpers\Classes\PlanHelper::planMenuCheck($userPlan, $key))
        @if (data_get($item, 'is_admin'))
            @if ($isAdmin && $user->checkPermission($key))
                @if (data_get($item, 'show_condition', true) && data_get($item, 'is_active'))
                    @if ($item['children_count'])
                        @includeIf('default.components.navbar.partials.types.item-dropdown')
                    @else
                        @includeIf('default.components.navbar.partials.types.' . $item['type'])
                    @endif
                @endif
            @endif
        @else
            @if (data_get($item, 'show_condition', true) && data_get($item, 'is_active'))
                @if ($item['children_count'])
                    @includeIf('default.components.navbar.partials.types.item-dropdown')
                @else
                    @includeIf('default.components.navbar.partials.types.' . $item['type'])
                @endif
            @endif
        @endif
    @endif
@endforeach
