<?php

namespace App\Http\Controllers\Common;

use App\Helpers\Classes\Localization;
use App\Http\Controllers\Controller;

class LocaleController extends Controller
{
    public function __invoke(string $lang)
    {
        Localization::setLocale($lang);

        return redirect()->back()->with([
            'type'    => 'success',
            'message' => trans('Change language'),
        ]);
    }
}
