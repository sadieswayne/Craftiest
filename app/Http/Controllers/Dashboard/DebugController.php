<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class DebugController extends Controller
{
    public function __invoke($token)
    {
        $currentDebugValue = env('APP_DEBUG', false);
        $newDebugValue = ! $currentDebugValue;
        $envContent = file_get_contents(base_path('.env'));
        $envContent = preg_replace('/^APP_DEBUG=.*/m', 'APP_DEBUG=' . ($newDebugValue ? 'true' : 'false'), $envContent);
        file_put_contents(base_path('.env'), $envContent);
        Artisan::call('config:clear');

        return redirect()->back()->with('message', 'Debug mode updated successfully.');
    }
}
