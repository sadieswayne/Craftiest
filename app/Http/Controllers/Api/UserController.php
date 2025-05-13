<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateActivity;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a user profile.
     *
     * Get the authenticated user's profile.
     *
     * @OA\Get(
     *      path="/api/auth/profile",
     *      operationId="UserController::index",
     *      tags={"User Profile"},
     *      summary="Get user profile",
     *      description="Get the profile of the authenticated user.",
     *      security={{ "passport": {} }},
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
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->revenuecat_id == null) {
            // Added here because there is no other way to use RevenueCat's API rather than using the SDK
            // which is added to mobile app only. Workflow starts from mobile app.
            // Also, by this way we are sure that revenuecat_id is unique and not null.
            $user->revenuecat_id = Str::random(32);
            $user->save();
        }

        return $request->user();
    }

    /**
     * Show the form for creating a new users.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created users in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified users.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified users.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified users in storage.
     *
     * @OA\Post(
     *      path="/api/auth/profile",
     *      operationId="UserController::update",
     *      tags={"User Profile"},
     *      summary="!!PATCH REQUEST!! see payload '_method' Update user profile -  post used only for test",
     *      description="Updates the user's profile information, including name, surname, phone, country, password, and avatar.",
     *      security={{ "passport": {} }},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="User data",
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *                  type="object",
     *
     *                  @OA\Property(
     *                      property="name",
     *                      description="User's first name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="_method",
     *                     description="ID of the chat template",
     *                     type="string",
     *                     default="PATCH",
     *                 ),
     *                  @OA\Property(
     *                      property="surname",
     *                      description="User's last name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      description="User's phone number",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="country",
     *                      description="User's country",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="old_password",
     *                      description="User's old password for verification",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="new_password",
     *                      description="User's new password",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="new_password_confirmation",
     *                      description="Confirmation of the new password",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="avatar",
     *                      description="User's avatar image file (allowed types: jpg, png; max size: 5MB)",
     *                      type="string",
     *                      format="binary"
     *                  ),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User settings saved successfully",
     *
     *          @OA\MediaType(mediaType="application/json")
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *      @OA\Response(
     *          response=419,
     *          description="Validation error or unsupported file extension",
     *      ),
     * )
     */
    public function update(Request $request)
    {
        $user = $request->user();
        if ($request->name != null) {
            $user->name = $request->name;
        }
        if ($request->surname != null) {
            $user->surname = $request->surname;
        }
        if ($request->phone != null) {
            $user->phone = $request->phone;
        }
        if ($request->country != null) {
            $user->country = $request->country;
        }

        if ($request->old_password != null) {
            $validated = Validator::make($request->all(), [
                'old_password' => ['required', 'current_password'],
                'new_password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            if ($validated->fails()) {
                return response()->json(['error' => $validated->errors()], 422);
            }

            $user->password = Hash::make($request->new_password);
        }

        if ($request->hasFile('avatar')) {
            $path = 'upload/images/avatar/';
            $image = $request->file('avatar');

            if ($image->getClientOriginalExtension() == 'svg') {
                $image = self::sanitizeSVG($request->file('avatar'));
            }

            $image_name = Str::random(4) . '-' . Str::slug($user?->fullName()) . '-avatar.' . $image->getClientOriginalExtension();

            // Image extension check
            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                return response()->json(['error' => __('The file extension must be jpg, jpeg, png, webp or svg.')], 419);
            }

            $image->move($path, $image_name);

            $user->avatar = $path . $image_name;
        }

        CreateActivity::for($user, 'Updated', 'Profile Information');
        $user->save();

        return response()->json(['message' => 'User settings saved successfully'], 200);
    }

    /**
     * Delete user account
     *
     * Get the authenticated user's profile and delete account.
     *
     * @OA\Delete(
     *      path="/api/auth/profile",
     *      operationId="UserController::destroy",
     *      tags={"User Profile"},
     *      summary="Delete user account",
     *      description="Get the profile of the authenticated user and delete account.",
     *      security={{ "passport": {} }},
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
     * )
     */
    public function destroy(Request $request)
    {

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => __('User not found'),
            ], 404);
        }

        CreateActivity::for($user, 'Deleted', $user?->fullName() . ' deleted his/her account.');

        // All user data should be deleted from the database via cascade delete
        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => __('User deleted successfully'),
        ], 200);
    }
}
