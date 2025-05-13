<?php

namespace App\Http\Controllers\Api;

use App\Actions\EmailConfirmation;
use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Contracts\WithCreditInterface;
use App\Domains\Entity\EntityStats;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\SendPasswordResetEmail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/auth/register",
     *      operationId="register",
     *      tags={"Authentication"},
     *      summary="Register a new user",
     *      description="Registers a new user with the provided data",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name", "surname", "email", "password", "password_confirmation"},
     *
     *              @OA\Property(property="name", type="string", example="John"),
     *              @OA\Property(property="surname", type="string", example="Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *              @OA\Property(property="affiliate_code", type="string", nullable=true, example="your_affiliate_code"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="User registered successfully",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error or user already exists",
     *      ),
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'surname'  => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $affCode = null;
        if ($request->affiliate_code !== null) {
            $affUser = User::where('affiliate_code', $request->affiliate_code)->first();
            if ($affUser !== null) {
                $affCode = $affUser->id;
            }
        }

        if (Helper::appIsDemo()) {
            $user = User::query()->create([
                'name'                    => $request->name,
                'surname'                 => $request->surname,
                'email'                   => $request->email,
                'email_confirmation_code' => Str::random(67),
                'password'                => Hash::make($request->password),
                'email_verification_code' => Str::random(67),
                'affiliate_id'            => $affCode,
                'affiliate_code'          => Str::upper(Str::random(12)),
            ]);
            EntityStats::all()->map(function ($entity) use ($user) {
                return $entity->forUser($user)->list()->each(function (EntityDriverInterface&WithCreditInterface $entity) {
                    return $entity->setDefaultCreditForDemo();
                });
            });
        } else {
            $user = User::query()->create([
                'name'                    => $request->name,
                'surname'                 => $request->surname,
                'email'                   => $request->email,
                'email_confirmation_code' => Str::random(67),
                'password'                => Hash::make($request->password),
                'email_verification_code' => Str::random(67),
                'affiliate_id'            => $affCode,
                'affiliate_code'          => Str::upper(Str::random(12)),
            ]);

            $user->updateCredits(setting('freeCreditsUponRegistration', User::getFreshCredits()));
        }

        return response()->json('OK', 200);
    }

    /**
     * @OA\Post(
     *      path="/api/auth/logout",
     *      operationId="logout",
     *      tags={"Authentication"},
     *      summary="Logout the authenticated user",
     *      description="Logs out the authenticated user and revokes the access token",
     *      security={{ "passport": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Logout successful",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json(['message' => 'Logout successful'], 200);
    }

    /**
     * Send a password reset link to the given user.
     *
     * @OA\Post(
     *      path="/api/auth/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"Authentication"},
     *      summary="Initiate password reset",
     *      description="Initiate the password reset process by sending an email with a reset link.",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"email"},
     *
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Password reset link sent successfully",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error or user not found",
     *      ),
     * )
     */
    public function sendPasswordResetMail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user !== null) {
            $user->password_reset_code = Str::random(67);
            $user->save();

            // Dispatch the job to send the password reset email asynchronously
            dispatch(new SendPasswordResetEmail($user));

            return response()->json(['message' => __('Password reset link sent successfully')], 200);
        }

        return response()->json(['error' => __('User not found')], 422);
    }

    /**
     * Verify user's email using the provided confirmation code.
     *
     * @OA\Get(
     *      path="/api/auth/email/verify",
     *      operationId="verifyEmail",
     *      tags={"Authentication"},
     *      summary="Verify user's email",
     *      description="Verify the user's email using the provided confirmation code.",
     *
     *      @OA\Parameter(
     *          name="email_confirmation_code",
     *          in="query",
     *          required=true,
     *          description="Email confirmation code",
     *
     *          @OA\Schema(type="string"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Email verified successfully",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error or user not found",
     *      ),
     * )
     */
    public function emailConfirmationMail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_confirmation_code' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('email_confirmation_code', $request->email_confirmation_code)->first();

        if ($user !== null) {
            $user->email_confirmation_code = null;
            $user->email_confirmed = 1;
            $user->status = 1;
            $user->save();

            return response()->json(['message' => 'Email verified successfully'], 200);
        }

        return response()->json(['error' => __('Email not found')], 422);
    }

    /**
     * Resend the confirmation email.
     *
     * @OA\Post(
     *      path="/api/auth/email/verify/resend",
     *      operationId="resendConfirmationEmail",
     *      tags={"Authentication"},
     *      summary="Resend confirmation email",
     *      description="Resend the confirmation email to the user if not already verified.",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"email"},
     *
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Confirmation email resent successfully",
     *
     *          @OA\JsonContent(
     *              type="object",
     *              example={"message": "Confirmation email resent successfully"},
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=403,
     *          description="Email already verified",
     *
     *          @OA\JsonContent(
     *              type="object",
     *              example={"error": "Email already verified"},
     *          ),
     *      ),
     * )
     */
    public function resend(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if (! $user->isConfirmed() && ! $user->isAdmin()) {
            EmailConfirmation::forUser($user)->resend();

            return response()->json(['message' => __('Confirmation email resent successfully')], 200);
        }

        return response()->json(['error' => __('Email not found or already verified')], 403);
    }

    /**
     * Get actively supported login methods.
     *
     * @OA\Get(
     *      path="/api/auth/social-login",
     *      operationId="getSupportedLoginMethods",
     *      tags={"Authentication"},
     *      summary="Get supported login methods",
     *      description="Get actively supported login methods as a list.",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="array",
     *
     *              @OA\Items(
     *                  type="string",
     *                  example="github",
     *              ),
     *              example={"github", "google"},
     *          ),
     *      ),
     * )
     */
    public function getSupportedLoginMethods(): JsonResponse
    {
        $setting = Setting::getCache();

        $supportedLoginMethods = [];

        if ($setting->github_active) {
            $supportedLoginMethods[] = 'github';
        }

        if ($setting->twitter_active) {
            $supportedLoginMethods[] = 'twitter';
        }

        if ($setting->google_active) {
            $supportedLoginMethods[] = 'google';
        }

        if ($setting->facebook_active) {
            $supportedLoginMethods[] = 'facebook';
        }

        return response()->json($supportedLoginMethods, 200);
    }
}
