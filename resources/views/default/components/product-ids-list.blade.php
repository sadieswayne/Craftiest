@if($gatewayProducts && $gatewayProducts->isNotEmpty())
    <div class="mt-20">
        <h4 class="mb-4">
            {{ __('These values are generated for you') }}
        </h4>
        <x-table class="text-2xs">
            <x-slot:head>
                <th>
                    {{ __('Gateway') }}
                </th>
                <th>
                    {{ __('Product ID') }}
                </th>
                <th>
                    {{ __('Plan / Price ID') }}
                </th>
            </x-slot:head>
            <x-slot:body>
                @foreach ($gatewayProducts as $product)
                    <tr class="even:bg-foreground/5">
                        <td>
                            {{ $product->gateway_title }}
                        </td>
                        <td>
                            {{ $product->product_id }}
                        </td>
                        <td>
                            {{ $product->price_id }}
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
@endif
