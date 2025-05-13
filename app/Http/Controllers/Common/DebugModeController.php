<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class DebugModeController extends Controller
{
    public function __invoke($token)
    {
        $storedHash = Config::get('app.debug_hash');
        $hashedToken = Hash::make($token);
        if (Hash::check($token, $storedHash)) {
            $currentDebugValue = env('APP_DEBUG', false);
            $newDebugValue = ! $currentDebugValue;
            $envContent = file_get_contents(base_path('.env'));
            $envContent = preg_replace('/^APP_DEBUG=.*/m', 'APP_DEBUG=' . ($newDebugValue ? 'true' : 'false'), $envContent);
            file_put_contents(base_path('.env'), $envContent);
            Artisan::call('config:clear');

            return redirect()->back()->with('message', 'Debug mode updated successfully.');
        }

        return 'Invalid token!';
    }
}
