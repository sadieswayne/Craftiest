<x-navbar.item>
	<x-navbar.divider />
</x-navbar.item>

<x-navbar.item class="group-[&.navbar-shrinked]/body:hidden">
	<x-navbar.label>
		{{ __('Credits') }}
	</x-navbar.label>
</x-navbar.item>

<x-navbar.item class="pb-navbar-link-pb pe-navbar-link-pe ps-navbar-link-ps pt-navbar-link-pt group-[&.navbar-shrinked]/body:hidden">
	<x-credit-list />
</x-navbar.item>
