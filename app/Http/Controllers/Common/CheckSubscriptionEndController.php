<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class CheckSubscriptionEndController extends Controller
{
    public function __invoke()
    {
        Artisan::call('schedule:run');

        return 'Schedule initiated.';
    }
}
