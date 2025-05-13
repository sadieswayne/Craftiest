@extends('panel.layout.app')
@section('title', __('Mobile Subscriptions and Packs'))
@section('titlebar_actions', '')
@section('content')
    <div class="py-10">
        @if ($settings_two->liquid_license_type != 'Extended License')
            <p class="w-full rounded-xl bg-red-100 p-3 text-center text-red-600 dark:bg-orange-600/20 dark:text-orange-200">
                {{ __('To access this page, you should upgrade to Extended License.') }}
                <a
                    class="underline"
                    href="{{ route('LaravelInstaller::license.upgrade') }}"
                >
                    {{ __('Upgrade License') }}
                </a>
            </p>
        @else
            <div class="flex flex-wrap justify-between gap-3 xl:flex-row xl:flex-nowrap">
                <x-table class:wrap="grow">
                    <x-slot:head>
                        <tr>
                            <th>
                                {{ __('Status') }}
                            </th>
                            <th>
                                {{ __('Name') }}&nbsp;/&nbsp;{{ __('Type') }}
                            </th>
                            <th>
                                {{ __('RC Package Id') }}
                            </th>
                            <th>
                                {{ __('RC Entitlement Id') }}
                            </th>
                            <th>
                                {{ __('RC Apple Id') }}
                            </th>
                            <th>
                                {{ __('RC Google Id') }}
                            </th>
                            <th class="text-end">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </x-slot:head>

                    <x-slot:body>
                        @foreach ($plans as $entry)
                            <tr data-row-id="{{ $entry->id }}">
                                <td>
                                    <x-badge
                                        class="text-2xs"
                                        variant="{{ $entry->active == 1 ? 'success' : 'danger' }}"
                                    >
                                        {{ $entry->active == 1 ? __('Active') : __('Passive') }}
                                    </x-badge>
                                </td>
                                @php
                                    $entryType = $entry->type == 'prepaid' ? __('Token Pack') : __('Subscription');
                                @endphp
                                <td>
                                    <p class="flex flex-col">
                                        {{ $entry->name }}
                                        <span class="block opacity-60">
                                            {{ $entry->type == 'prepaid' ? __('Token Pack') : __('Subscription') }}
                                        </span>
                                    </p>
                                </td>
                                <td>
                                    @php
                                        $foundRevenueCatProduct = false;
                                        $revenueCatProduct = '';
                                    @endphp
                                    @foreach ($entry->gatewayProducts as $prd)
                                        @if ($prd->gateway_code == 'revenuecat')
                                            @if ($prd->product_id != null)
                                                {{ $prd->product_id }}
                                                @php
                                                    $foundRevenueCatProduct = true;
                                                    $revenueCatProduct = $prd->product_id;
                                                @endphp
                                            @else
                                                {{ __('Not Set') }}
                                            @endif
                                        @endif
                                    @endforeach
                                    @if (!$foundRevenueCatProduct)
                                        {{ __('Not Set') }}
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $foundRevenueCatPrice = false;
                                        $revenueCatPrice = '';
                                    @endphp
                                    @foreach ($entry->gatewayProducts as $prd)
                                        @if ($prd->gateway_code == 'revenuecat')
                                            @if ($prd->price_id != null)
                                                {{ $prd->price_id }}
                                                @php
                                                    $foundRevenueCatPrice = true;
                                                    $revenueCatPrice = $prd->price_id;
                                                @endphp
                                            @else
                                                {{ __('Not Set') }}
                                            @endif
                                        @endif
                                    @endforeach
                                    @if (!$foundRevenueCatPrice)
                                        {{ __('Not Set') }}
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $foundRevenueCatApple = false;
                                        $revenueCatApple = '';
                                    @endphp
                                    @foreach ($entry->revenuecat_products as $prd)
                                        @if ($prd->apple_id != null)
                                            {{ $prd->apple_id }}
                                            @php
                                                $foundRevenueCatApple = true;
                                                $revenueCatApple = $prd->apple_id;
                                            @endphp
                                        @else
                                            {{ __('Not Set') }}
                                        @endif
                                    @endforeach
                                    @if (!$foundRevenueCatApple)
                                        {{ __('Not Set') }}
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $foundRevenueCatGoogle = false;
                                        $revenueCatGoogle = '';
                                    @endphp
                                    @foreach ($entry->revenuecat_products as $prd)
                                        @if ($prd->google_id != null)
                                            {{ $prd->google_id }}
                                            @php
                                                $foundRevenueCatGoogle = true;
                                                $revenueCatGoogle = $prd->google_id;
                                            @endphp
                                        @else
                                            {{ __('Not Set') }}
                                        @endif
                                    @endforeach
                                    @if (!$foundRevenueCatGoogle)
                                        {{ __('Not Set') }}
                                    @endif
                                </td>

                                <td class="whitespace-nowrap text-end">
                                    @if ($app_is_demo)
                                        <x-button
                                            class="size-9"
                                            variant="ghost-shadow"
                                            size="none"
                                            onclick="return toastr.info('This feature is disabled in Demo version.')"
                                            title="{{ __('Edit') }}"
                                        >
                                            <x-tabler-pencil class="size-4" />
                                        </x-button>
                                    @else
                                        <x-button
                                            class="size-9"
                                            variant="ghost-shadow"
                                            size="none"
                                            type="button"
                                            onclick="fillEditableFields('{{ $entry->id }}', '{{ $entry->name }}', '{{ $entryType }}', '{{ $revenueCatProduct }}', '{{ $revenueCatPrice }}', '{{ $revenueCatGoogle }}', '{{ $revenueCatApple }}')"
                                            title="{{ __('Edit') }}"
                                        >
                                            <x-tabler-pencil class="size-4" />
                                        </x-button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-slot:body>
                </x-table>

                <form method="POST">
                    @csrf
                    <div class="flex w-full flex-col gap-3 xl:w-72">
                        <x-card
                            class:body="flex flex-col gap-4"
                            size="sm"
                        >
                            <input
                                id="plan_id"
                                type="hidden"
                                name="plan_id"
                            />
                            <input
                                id="plan_name_input"
                                type="hidden"
                                name="plan_name_label"
                            />
                            <input
                                id="plan_type_input"
                                type="hidden"
                                name="plan_type_label"
                            />

                            <h4 class="w-full border-b pb-3">
                                {{ __('Update Plan') }}
                            </h4>

                            <p class="text-2xs">
                                <span class="opacity-80">{{ __('Plan Name') }} : </span>
                                <span
                                    class="ps-1 font-semibold"
                                    id="plan_name_label"
                                ></span>
                            </p>

                            <p class="-mt-2 text-2xs">
                                <span class="opacity-80">{{ __('Plan Type') }} : </span>
                                <span
                                    class="ps-1 font-semibold"
                                    id="plan_type_label"
                                ></span>
                            </p>

                            <x-forms.input
                                id="revenuecat_package_id"
                                size="lg"
                                name="revenuecat_package_id"
                                required
                                placeholder="{{ __('Please enter Package Identifier') }}"
                                label="{{ __('RevenueCat Package Id') }}"
                            />

                            <x-forms.input
                                id="revenuecat_entitlement_id"
                                size="lg"
                                name="revenuecat_entitlement_id"
                                required
                                placeholder="{{ __('Please enter Entitlement Identifier') }}"
                                label="{{ __('RevenueCat Entitlement Id') }}"
                            />

                            <x-forms.input
                                id="revenuecat_apple_id"
                                size="lg"
                                name="revenuecat_apple_id"
                                required
                                placeholder="{{ __('Please enter Product Identifier') }}"
                                label="{{ __('RevenueCat Apple Product Id') }}"
                            />

                            <x-forms.input
                                id="revenuecat_google_id"
                                size="lg"
                                name="revenuecat_google_id"
                                required
                                placeholder="{{ __('Please enter Product Identifier') }}"
                                label="{{ __('RevenueCat Google Product Id') }}"
                            />

                            <x-button type="submit">
                                {{ __('Save') }}
                            </x-button>
                        </x-card>
                        <x-card
                            class:body="space-y-3"
                            size="sm"
                        >
                            <p class="font-semibold">
                                {{ __('Important:') }}
                            </p>
                            <p>
                                {{ __('In RevenueCat dashboard, create only one instance of offerings and set it as default. Mobile app checks for default offering and searches given package and entitlement ids in there.') }}
                            </p>
                            <p>
                                {{ __('Also, please do not set both identifiers identical. For instance, you can use _ent and _pac at the end of ids.') }}
                            </p>
                        </x-card>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <script>
        function fillEditableFields(planId, planName, planType, revenueCatPackageId, revenueCatEntitlementId,
            revenueCatGoogleId, revenueCatAppleId) {
            document.getElementById('plan_id').value = planId;
            document.getElementById('plan_name_label').innerText = planName;
            document.getElementById('plan_name_input').value = planName;
            document.getElementById('plan_type_label').innerText = planType;
            document.getElementById('plan_type_input').value = planType;
            document.getElementById('revenuecat_package_id').value = revenueCatPackageId;
            document.getElementById('revenuecat_entitlement_id').value = revenueCatEntitlementId;
            document.getElementById('revenuecat_google_id').value = revenueCatGoogleId;
            document.getElementById('revenuecat_apple_id').value = revenueCatAppleId;
        }
    </script>

@endsection
