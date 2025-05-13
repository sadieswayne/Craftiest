<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    // / Social Login with Google
    /**
     * Social Login with Google
     *
     * @OA\Post(
     *      path="/api/auth/google-login",
     *      operationId="google",
     *      tags={"Authentication"},
     *      summary="Social Login with Google",
     *      description="Social Login with Google",
     *      security={{ "passport": {} }},
     *
     *      @OA\RequestBody(
     *         required=true,
     *         description="Google token data",
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="google_token",
     *                     description="Google Access Token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="google_id",
     *                     description="Google User ID (OpenID)",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
     */
    public function google(Request $request): JsonResponse
    {

        if (empty($request->google_id) || empty($request->google_token)) {
            return response()->json([
                'status'  => false,
                'message' => __('Invalid token'),
            ], 412);
        }

        $googleUser = Socialite::driver('google')->userFromToken($request->google_token);

        if (! $googleUser) {
            Log::info('Sign in with Google - Invalid token - User not found');

            return response()->json([
                'status'  => false,
                'message' => __('Invalid token - User not found'),
            ], 412);

        }

        $checkUser = User::where('email', $googleUser->getEmail())->exists();
        $nameParts = explode(' ', $googleUser->getName());
        $name = $nameParts[0] ?? '';
        $surname = $nameParts[1] ?? '';

        if ($checkUser) {
            $user = User::where('email', $googleUser->getEmail())->first();
            $user->google_token = $googleUser->token;
            $user->google_refresh_token = $googleUser->refreshToken;
            $userSocialAvatar = $googleUser->getAvatar() ?? ($user->avatar ?? 'assets/img/auth/default-avatar.png');
            $user->avatar = $user->avatar === 'assets/img/auth/default-avatar.png' ? $userSocialAvatar : $user->avatar;
            $user->affiliate_code = $user->affiliate_code ?? Str::upper(Str::random(12));
            $user->save();
        } else {
            $user = User::query()->updateOrCreate([
                'google_id' => $googleUser->id,
            ], [
                'name'                 => $name,
                'surname'              => $surname,
                'email'                => $googleUser->getEmail(),
                'google_token'         => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar'               => $googleUser->getAvatar(),
                'password'             => Hash::make(Str::random(12)),
                'affiliate_code'       => Str::upper(Str::random(12)),
                'email_verified_at'    => now(),
                'email_confirmed'      => true,
            ]);
            $user->updateCredits(setting('freeCreditsUponRegistration', User::getFreshCredits()));
        }

        $token = $user->createToken('google')->accessToken;

        if ($token !== null) {
            return response()->json([
                'access_token' => $token,
            ], 200);
        } else {
            Log::info('Sign in with Google - Invalid passport token');

            return response()->json([
                'status'  => false,
                'message' => __('Invalid passport token'),
            ], 412);
        }

    }

    // / Social Login with Apple
    /**
     * Social Login with Apple
     *
     * @OA\Post(
     *      path="/api/auth/apple-login",
     *      operationId="apple",
     *      tags={"Authentication"},
     *      summary="Social Login with Apple",
     *      description="Social Login with Apple",
     *      security={{ "passport": {} }},
     *
     *      @OA\RequestBody(
     *         required=true,
     *         description="Apple token data",
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="apple_token",
     *                     description="Apple Access Token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="apple_id",
     *                     description="Apple User ID (OpenID)",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
     */
    public function apple(Request $request): JsonResponse
    {
        if (empty($request->apple_id) || empty($request->apple_token)) {
            return response()->json([
                'status'  => false,
                'message' => __('Invalid token'),
            ], 412);
        }

        try {
            $appleUser = Socialite::driver('apple')->userFromToken($request->apple_id);
        } catch (Exception $e) {
            Log::error('Sign in with Apple : ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => __('Invalid token - User not found'),
            ], 500);
        }

        if (! $appleUser) {
            Log::warning('Sign in with Apple - Invalid token - User not found');

            return response()->json([
                'status'  => false,
                'message' => __('Invalid token - User not found'),
            ], 412);

        }

        $checkUser = User::where('email', $appleUser->getEmail())->exists();
        $nameParts = explode(' ', $appleUser->getName());
        $name = $nameParts[0] ?? '';
        $surname = $nameParts[1] ?? '';

        if ($checkUser) {
            $user = User::where('email', $appleUser->getEmail())->first();
            $user->apple_token = $appleUser->token;
            $user->apple_refresh_token = $appleUser->refreshToken;
            $userSocialAvatar = $appleUser->getAvatar() ?? ($user->avatar ?? 'assets/img/auth/default-avatar.png');
            $user->avatar = $user->avatar === 'assets/img/auth/default-avatar.png' ? $userSocialAvatar : $user->avatar;
            $user->affiliate_code = $user->affiliate_code ?? Str::upper(Str::random(12));
            $user->save();
        } else {
            $user = User::query()->updateOrCreate([
                'apple_id' => $appleUser->id,
            ], [
                'name'                => $name,
                'surname'             => $surname,
                'email'               => $appleUser->getEmail(),
                'apple_token'         => $appleUser->token,
                'apple_refresh_token' => $appleUser->refreshToken,
                'avatar'              => $appleUser->getAvatar() ?? 'assets/img/auth/default-avatar.png',
                'password'            => Hash::make(Str::random(12)),
                'affiliate_code'      => Str::upper(Str::random(12)),
                'email_verified_at'   => now(),
                'email_confirmed'     => true,
            ]);
            $user->updateCredits(setting('freeCreditsUponRegistration', User::getFreshCredits()));
        }

        $token = $user->createToken('apple')->accessToken;
        if ($token) {
            return response()->json([
                'access_token' => $token,
            ], 200);
        }

        Log::info('Sign in with Apple - Invalid passport token');

        return response()->json([
            'status'  => false,
            'message' => __('Invalid passport token'),
        ], 412);

    }
}
