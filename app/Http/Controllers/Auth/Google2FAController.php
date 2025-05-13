<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Google2FA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Google2FAController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('login', 'verify');
    }

    public function activate2FA(Request $request)
    {
        // use existing secret key if exist (e.g., error during setup verification)
        // otherwise generate secret key
        $secret = $request->session()->get('google2fa_secret') ?? Google2FA::generateSecretKey();

        // store secret in the session only for the next request
        $request->session()->flash('google2fa_secret', $secret);

        // generate image for QR barcode
        $qrCode = Google2FA::getQRCodeInline(
            config('app.name'),
            $request->user()->email,
            $secret,
            200
        );

        return view('panel.user.settings.google2fa', [
            'qrCode' => $qrCode,
            'secret' => $secret,
        ]);
    }

    public function assign2FA(Request $request)
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $request->validate([
            'one_time_password' => 'required|digits:6',
            'google2fa_secret'  => 'required',
        ]);

        $verificationCode = $request->get('one_time_password');
        $secret = $request->session()->get('google2fa_secret') ?? $request->get('google2fa_secret');

        if (! $secret || ! Google2FA::verifyGoogle2FA($secret, $verificationCode)) {
            // store secret in the session only for the next request
            $request->session()->reflash();

            throw ValidationException::withMessages([
                'one_time_password' => [trans('auth.2fa.failed')],
            ]);
        }

        $user = $request->user();
        $user->google2fa_secret = $secret;
        $user->save();

        session()->put('save_login_2fa', true);
        session()->save();

        return back()->with('success', '2FA has been enabled successfully.');
    }

    /**
     * Deactivate 2FA.
     *
     * @return \Illuminate\Http\Response
     */
    public function deactivate2FA(Request $request)
    {
        $user = $request->user();

        // make secret column blank
        $user->google2fa_secret = null;
        $user->save();

        return back()->with('success', '2FA has been disabled successfully.');
    }

    public function login()
    {
        if (session()->has('user_id')) {
            return view('panel.authentication.2fa');
        }

        return to_route('login');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
            'user_id'           => 'required',
        ]);

        $userId = session('user_id');

        $user = User::find($userId);

        $otp = $request->get('one_time_password');

        if (! $user || ! Google2FA::verifyGoogle2FA($user->google2fa_secret, $otp)) {
            // store secret in the session only for the next request
            $request->session()->reflash();
            // re-flash request inputs
            $request->flash();

            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ])->redirectTo(route('login'));
        }

        Auth::login($user);

        session()->put('save_login_2fa', true);
        session()->save();

        return redirect(RouteServiceProvider::HOME);
    }
}
