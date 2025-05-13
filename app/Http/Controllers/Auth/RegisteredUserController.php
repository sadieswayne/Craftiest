<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('panel.authentication.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::query()->create([
            'name'                    => $request->name,
            'email'                   => $request->email,
            'password'                => Hash::make($request->password),
            'email_verification_code' => Str::random(67),
            'affiliate_code'          => Str::upper(Str::random(19)),
        ]);

        $user->updateCredits(setting('freeCreditsUponRegistration', User::getFreshCredits()));

        // event(new Registered($user));
        $settings = Setting::getCache();
        if ($settings->login_without_confirmation === 1) {
            Auth::login($user);
        }

        return response()->json('OK');
    }
}
