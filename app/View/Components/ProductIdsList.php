<?php

namespace App\View\Components;

use App\Models\GatewayProducts;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class ProductIdsList extends Component
{
    public GatewayProducts|Collection|null $gatewayProducts;

    /**
     * Create a new component instance.
     */
    public function __construct(GatewayProducts|Collection|null $gatewayProducts)
    {
        $this->gatewayProducts = $gatewayProducts;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-ids-list');
    }
}
