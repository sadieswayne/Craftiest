<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OpenaiGeneratorChatCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatTemplatesController extends Controller
{
    /**
     * Display a listing of the chat templates. If id sent thats will show single template
     *
     * @OA\Get(
     *      path="/api/aichat/chat-templates",
     *      operationId="ChatTemplatesController::index",
     *      tags={"Chat Templates"},
     *      summary="Get a list of chat templates or single one if id passed",
     *      description="Get a list of chat templates ordered by name in ascending order. If an ID is provided, it will retrieve a single chat template.",
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
    public function index($id = null)
    {
        if ($id == 'undefined' || $id == null) {
            $list = OpenaiGeneratorChatCategory::orderBy('name', 'asc')->get();
        } else {
            $list = OpenaiGeneratorChatCategory::find($id);
            if (! $list) {
                return response()->json(['error' => __('Template Not Found')], 404);
            }
        }

        return response()->json($list, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/aichat/chat-templates",
     *     summary="!!PATCH REQUEST!! see payload '_method' Update a chat template -  post used only for test",
     *     operationId="ChatTemplatesController@update",
     *     tags={"Chat Templates"},
     *     security={{ "passport": {} }},
     *     description="Update the specified chat template in storage. If `template_id` is 'undefined' or `null`, a new template will be created.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Chat template data",
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                  @OA\Property(
     *                     property="_method",
     *                     description="ID of the chat template",
     *                     type="string",
     *                     default="PATCH",
     *                 ),
     *                 @OA\Property(
     *                     property="template_id",
     *                     description="ID of the chat template",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of the chat template",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="short_name",
     *                     description="Short name of the chat template",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Description of the chat template",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="role",
     *                     description="Role of the chat template",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="human_name",
     *                     description="Human name of the chat template",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="helps_with",
     *                     description="How the chat template helps",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="color",
     *                     description="Color of the chat template",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="chat_completions",
     *                     description="Chat completions",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     description="Avatar image file (allowed types: jpg, jpeg, png, svg, webp; max size: 5MB)",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Template edited successfully"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=419,
     *         description="Validation error or unsupported file extension",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string")),
     *         ),
     *     ),
     * )
     */
    public function update(Request $request)
    {

        if ($request->template_id == 'undefined' || $request->template_id == null) {
            $template = new OpenaiGeneratorChatCategory;
            $res = __('Template added successfully');
        } else {
            $template = OpenaiGeneratorChatCategory::where('id', $request->template_id)->firstOrFail();
            $res = __('Template updates successfully');
        }

        if ($request->hasFile('avatar')) {
            $path = 'upload/images/chatbot/';
            $image = $request->file('avatar');
            $image_name = Str::random(4) . '-' . Str::slug($request->name) . '-avatar.' . $image->getClientOriginalExtension();

            // Resim uzantı kontrolü
            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                $data = [
                    'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                ];

                return response()->json($data, 419);
            }

            $image->move($path, $image_name);

            $template->image = $path . $image_name;
        }

        $template->name = $request->name;
        $template->slug = Str::slug($request->name) . '-' . Str::random(5);
        $template->short_name = $request->short_name;
        $template->description = $request->description;
        $template->role = $request->role;
        $template->human_name = $request->human_name;
        $template->helps_with = $request->helps_with;
        $template->color = $request->color;
        $template->chat_completions = $request->chat_completions;
        $template->prompt_prefix = 'As a ' . $request->role . ', ';
        $template->save();

        return response()->json(['message' => $res], 200);
    }

    /**
     * Remove the specified chat templates from storage.
     *
     * @OA\Delete(
     *     path="/api/aichat/chat-templates/{id}",
     *     summary="Remove a chat template",
     *     operationId="ChatTemplatesController@destroy",
     *     tags={"Chat Templates"},
     *     security={{ "passport": {} }},
     *     description="Remove the specified chat template from storage.",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the chat template",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Chat template deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chat template not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Chat template not found"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *         ),
     *     ),
     * )
     */
    public function destroy($id = null)
    {
        if ($id == null) {
            return response()->json(['error' => __('ID required')], 412);
        }

        $template = OpenaiGeneratorChatCategory::where('id', $id)->firstOrFail();
        $template->delete();

        return response()->json(['message' => 'Deleted Successfully'], 200);
    }
}
