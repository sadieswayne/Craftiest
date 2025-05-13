<?php

namespace App\Http\Controllers\Auth;

use App\Actions\EmailConfirmation;
use App\Events\UsersActivityEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\OtpEmail;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Google2FA;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('panel.authentication.login', [
            'plan' => request('plan'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $settings = Setting::getCache();
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($settings->recaptcha_login && ($settings->recaptcha_sitekey || $settings->recaptcha_secretkey)) {
            $response = (new Client)->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => config('services.recaptcha.secret'),
                    'response' => $request->input('g-recaptcha-response'),
                ],
            ])->getBody()->getContents();

            if (! json_decode($response, true)['success']) {
                return response()->json(['status' => 'error', 'message' => __('Invalid Recaptcha.')], 401);
            }
        }

        if (! $settings->login_without_confirmation) {
            if (! $user) {
                return response()->json(['errors' => [trans('auth.failed')]], 401);
            }

            if (! $user->email_confirmed && ! $user->isAdmin()) {
                EmailConfirmation::forUser($user)->send();

                return response()->json([
                    'errors' => [__('We have sent you an email for account confirmation. Please confirm your account to continue. Please check spam folder. If not received, try login again after 1 hour.')],
                    'type'   => 'confirmation',
                ], 401);
            }
        }

        if ($settings->login_with_otp) {
            if (! $user) {
                return response()->json(['errors' => [trans('auth.failed')]], 401);
            }

            $otp = mt_rand(1000, 9999);
            $user->update(['otp' => $otp]); // One DB write instead of save()

            try {
                Mail::to($user->email)->send(new OtpEmail($user, $settings, $otp));
            } catch (Exception) {
                return response()->json(['errors' => [__('Email could not be sent.')], 'type' => 'error'], 401);
            }

            return response()->json(['link' => '/verify-otp']);
        }

        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::check()) {
            $user = Auth::user();

            if (Google2FA::isActivated()) {
                session(['user_id' => $user->id]);
                Auth::logout();

                return response()->json(['link' => '2fa/login']);
            }

            event(new UsersActivityEvent($user->email, $user->type, $request->ip(), $request->header('User-Agent')));
        }

        return response()->json([
            'link' => $request->get('plan') ? '/dashboard/user/payment?plan=' . $request->get('plan') : '/dashboard/user',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function verifyOtpCode()
    {
        return view('panel.authentication.otp_verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|array',
        ]);

        $otp = implode('', $request->get('otp'));

        $user = User::query()->where('otp', $otp)->first();

        if (! $user) {
            return response()->json([
                'errors' => [__('Invalid OTP Code')],
                'type'   => 'otp',
            ], 422);
        }

        $user->otp = null;
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'link' => '/dashboard',
        ]);
    }
}
