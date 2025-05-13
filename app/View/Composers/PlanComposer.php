<?php

namespace App\View\Composers;

use App\Helpers\Classes\PlanHelper;
use Illuminate\View\View;

class PlanComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {

        $plan = PlanHelper::userPlan();

        $view->with([
            'userPlan' => $plan,
        ]);
    }
}
