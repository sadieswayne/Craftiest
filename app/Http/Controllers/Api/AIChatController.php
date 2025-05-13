<?php

namespace App\Http\Controllers\Api;

use App\Domains\Entity\Facades\Entity;
use App\Helpers\Classes\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorChatCategory;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\User;
use App\Models\UserOpenai;
use App\Models\UserOpenaiChat;
use App\Models\UserOpenaiChatMessage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;
use OpenAI\Laravel\Facades\OpenAI;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AIChatController extends Controller
{
    protected $settings;

    protected $settings_two;

    public function __construct()
    {
        // Settings
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
        $apiKey = ApiHelper::setOpenAiKey();
        config(['openai.api_key' => $apiKey]);
        set_time_limit(120);
    }

    // Conversations under (/history) -----------------------------------------------------

    /**
     *  get chatbot conversations by template category slug.
     *
     * @OA\Get(
     *      path="/api/aichat/history/{cat_slug}",
     *      operationId="getOpenAIChat",
     *      tags={"AI Chats (Convesations)"},
     *      security={{ "passport": {} }},
     *      summary="Get chatbot conversations",
     *
     *      @OA\Parameter(
     *          name="cat_slug",
     *          in="path",
     *          description="cat_slug of the OpenAI chat category",
     *          required=true,
     *
     *          @OA\Schema(type="string", example="ai-chat-bot"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="category", type="object"),
     *              @OA\Property(property="conversations", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Category not found",
     *      ),
     * )
     */
    public function openAIChat(Request $request, $cat_slug)
    {
        $category = OpenaiGeneratorChatCategory::whereSlug($cat_slug)->firstOrFail();

        $conversations = UserOpenaiChat::where('user_id', $request->user()->id)
            ->where('openai_chat_category_id', $category->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'category'      => $category,
            'conversations' => $conversations,
        ], 200);
    }

    /**
     *  get chatbot conversations by last 20 recent documents.
     *
     * @OA\Get(
     *      path="/api/aichat/recent-chats",
     *      operationId="recentChats",
     *      tags={"AI Chats (Convesations)"},
     *      security={{ "passport": {} }},
     *      summary="Get recent chatbot conversations",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="conversations", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     * )
     */
    public function recentChats(Request $request)
    {

        $conversations = UserOpenaiChat::where('user_id', $request->user()->id)
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($conversations, 200);
    }

    /**
     *  Search all chatbot conversations and return last 20 recent.
     *
     * @OA\Post(
     *      path="/api/aichat/search-recent-chats",
     *      operationId="searchRecentChats",
     *      tags={"AI Chats (Convesations)"},
     *      security={{ "passport": {} }},
     *      summary="Search all chatbot conversations and return last 20",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="conversations", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     * )
     */
    public function searchRecentChats(Request $request)
    {
        $word = $request->input('word');

        if ($word == null) {
            return response()->json(['error' => __('Word missing.')], 412);
        }

        $conversations = UserOpenaiChat::where([
            ['user_id', '=', $request->user()->id],
            ['title', 'like', '%' . $word . '%'],
        ])
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($conversations, 200);
    }

    /**
     * Create new chat conversation
     *
     * @OA\Post(
     *      path="/api/aichat/new-chat",
     *      operationId="startNewChat",
     *      tags={"AI Chats (Convesations)"},
     *      summary="Create new chat conversation",
     *      description="Create new chat conversation",
     *      security={{ "passport": {} }},
     *
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request chat template data",
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                  @OA\Property(
     *                     property="category_id",
     *                     description="Category (Template) ID",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *      ),
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
    public function startNewChat(Request $request)
    {
        if ($request->category_id == null) {
            return response()->json(['error' => __('Category ID missing.')], 412);
        }

        $category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->firstOrFail();
        $chat = new UserOpenaiChat;
        $chat->user_id = Auth::id();
        $chat->openai_chat_category_id = $category->id;
        $chat->title = $category->name . ' Chat';
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->save();

        $message = new UserOpenaiChatMessage;
        $message->user_openai_chat_id = $chat->id;
        $message->user_id = Auth::id();
        $message->response = 'First Initiation';
        if ($category->role == 'default') {
            $output = __('Hi! I am') . ' ' . $category->name . __(', and I\'m here to answer all your questions');
        } else {
            $output = __('Hi! I am') . ' ' . $category->human_name . __(', and I\'m') . ' ' . $category->role . '. ' . $category->helps_with;
        }
        $message->output = $output;
        $message->hash = Str::random(256);
        $message->credits = 0;
        $message->words = 0;
        $message->save();

        return response()->json($chat, 200);
    }

    /**
     * Delete a user's OpenAI conversation.
     *
     * @OA\Delete(
     *      path="/api/aichat/history",
     *      operationId="deleteChat",
     *      tags={"AI Chats (Convesations)"},
     *      security={{ "passport": {} }},
     *      summary="Delete a conversation",
     *      description="Delete a conversation and all its messages",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Conversation ID to delete",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="conver_id", type="string", example="109"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Chat not found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
     */
    public function deleteChat(Request $request)
    {
        if ($request->conver_id == null) {
            return response()->json(['error' => __('ID required')], 412);
        }

        $chat = UserOpenaiChat::where('id', $request->conver_id)->first();

        if ($chat) {
            if ($request->user()->id != $chat->user_id) {
                // user does not own this conversation
                return response()->json(['error' => __('User does not own this conversation')], 403);
            }
            // Messages of the conversation are deleted by cascade property. So we delete directly.
            $chat->delete();

            return response()->json(['message' => 'Delete successful'], 200);
        } else {
            return response()->json(['error' => __('Chat not found')], 404);
        }
    }

    /**
     * Rename a user's OpenAI conversation.
     *
     * @OA\Patch(
     *      path="/api/aichat/history",
     *      operationId="renameChat",
     *      tags={"AI Chats (Convesations)"},
     *      summary="Rename a conversation",
     *      security={{ "passport": {} }},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Chat ID and new title",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="conver_id", type="string", example="109"),
     *              @OA\Property(property="title", type="string", example="New Chat Title"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Chat not found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
     */
    public function renameChat(Request $request)
    {

        if ($request->conver_id == null) {
            return response()->json(['error' => __('ID required')], 412);
        }
        if ($request->title == null) {
            return response()->json(['error' => __('Title required')], 412);
        }

        $chat = UserOpenaiChat::where('id', $request->conver_id)->first();

        if ($chat) {
            if ($request->user()->id != $chat->user_id) {
                // user does not own this conversation
                return response()->json(['error' => __('User does not own this conversation')], 403);
            }

            $chat->title = $request->title;
            $chat->save();

            return response()->json(['message' => 'Rename successful'], 200);
        } else {
            return response()->json(['error' => __('Chat not found')], 404);
        }
    }

    /**
     * Search for user's OpenAI chats based on the provided category and search word.
     *
     * @OA\Post(
     *      path="/api/aichat/search-history",
     *      operationId="searchChatHistory",
     *      tags={"AI Chats (Convesations)"},
     *      summary="Search for chats in template category",
     *      security={{ "passport": {} }},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Category ID and search word",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="category_id", type="integer", example=1),
     *              @OA\Property(property="search_word", type="string", example="keyword"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="html", type="string"),
     *          ),
     *      ),
     * )
     */
    public function searchChatHistory(Request $request)
    {
        $categoryId = $request->input('category_id');
        $search = $request->input('search_word');

        $list = UserOpenaiChat::where('user_id', $request->user()->id)
            ->where('openai_chat_category_id', $categoryId)
            ->orderBy('updated_at', 'desc')
            ->where('title', 'like', '%' . $search . '%')
            ->get();

        return response()->json($list, 200);
    }

    // streamming under (/chat) -----------------------------------------------------

    /**
     * @OA\Post(
     *     path="/api/aichat/chat/chat-send",
     *     summary="Process chat output",
     *     tags={"AI Chats (Convesations)"},
     *     operationId="chatOutputPost",
     *     security={{ "passport": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body for chat output",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="conver_id", type="integer", example="31"),
     *             @OA\Property(property="prompt", type="string", example="Your chat prompt goes here"),
     *             @OA\Property(property="category_id", type="integer", example="1"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="conver_id", type="integer", example="1"),
     *             @OA\Property(property="message_id", type="integer", example="123"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=419,
     *         description="No credits left",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), example={"You have no credits left. Please consider upgrading your plan."}),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Error from API call",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Error from API call. Please try again. If error persists again, please contact the system administrator."),
     *         ),
     *     ),
     * )
     *
     * @OA\Get(
     *     path="/api/aichat/chat/chat-send",
     *     summary="Retrieve chat details",
     *     operationId="chatOutputGet",
     *     tags={"AI Chats (Convesations)"},
     *     security={{ "passport": {} }},
     *
     *     @OA\Parameter(
     *         name="conver_id",
     *         in="query",
     *         description="ID of the chat",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="message_id",
     *         in="query",
     *         description="ID of the message",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\MediaType(
     *             mediaType="text/event-stream",
     *
     *             @OA\Schema(
     *                 type="string",
     *                 example="data: This is a streamed response\n\n",
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Chat or message not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Chat or message not found"),
     *         ),
     *     ),
     * )
     *
     * @throws JsonException
     */
    public function chatOutput(Request $request): JsonResponse|StreamedResponse
    {
        $driver = Entity::driver();
        if ($request->isMethod('get')) {
            $driver->redirectIfNoCreditBalance();
            $conver_id = $request->conver_id;
            $message_id = $request->message_id;
            $message = UserOpenaiChatMessage::whereId($message_id)->first();
            $prompt = $message->input;
            $chat = UserOpenaiChat::whereId($conver_id)->first(); // conversations
            // $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc')->take(4);
            // $lastThreeMessage = $lastThreeMessageQuery->get()->reverse();
            $lastThreeMessageQuery = $chat->messages()
                ->whereNotNull('input')
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get()
                ->reverse();
            $category = OpenaiGeneratorChatCategory::where('id', $chat->openai_chat_category_id)->first();
            $chat_completions = str_replace(["\r", "\n"], '', $category->chat_completions);
            if ($chat_completions) {
                $chat_completions = json_decode($chat_completions, true, 512, JSON_THROW_ON_ERROR);
                foreach ($chat_completions as $item) {
                    $history[] = [
                        'role'    => $item['role'],
                        'content' => $item['content'],
                    ];
                }
            } else {
                $history[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
            }

            if ($category->prompt_prefix !== null) {
                $prompt = "You will now play a character and respond as that character (You will never break character). Your name is $category->human_name.
                I want you to act as a $category->role." . $category->prompt_prefix;

                $history[] = [
                    'role'    => 'system',
                    'content' => $prompt,
                ];
            }

            if (count($lastThreeMessageQuery) > 1) {
                foreach ($lastThreeMessageQuery as $threeMessage) {
                    $history[] = ['role' => 'user', 'content' => $threeMessage->input];
                    if ($threeMessage->response !== null) {
                        $history[] = ['role' => 'assistant', 'content' => $threeMessage->response];
                    }
                }
            } else {
                $history[] = ['role' => 'user', 'content' => $prompt];
            }

            return response()->stream(function () use ($conver_id, $message_id, $history, $driver) {
                try {
                    $stream = OpenAI::chat()->createStreamed([
                        'model'             => $driver->enum()->value,
                        'messages'          => $history,
                        'presence_penalty'  => 0.6,
                        'frequency_penalty' => 0,
                    ]);
                } catch (Exception $exception) {
                    Log::info($exception);
                    $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message ' . $exception->getMessage();
                    // echo "data: $messageError";
                    echo "error: $messageError";  // defined error: to handle error in frontend
                    echo "\n\n";
                    // ob_flush();
                    flush();
                    echo 'data: [DONE]';
                    echo "\n\n";
                    // ob_flush();
                    flush();
                    usleep(50000);
                    $stream = []; // empty array to prevent further processing and $stream is undefined error
                }

                $total_used_tokens = 0;
                $output = '';
                $responsedText = '';

                // Disable output buffering
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }
                ob_start();

                foreach ($stream as $response) {
                    if (isset($response['choices'][0]['delta']['content'])) {
                        $message = $response['choices'][0]['delta']['content'];
                        $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += countWords($message);

                        echo $messageFix;
                        echo PHP_EOL;
                        ob_flush();
                        flush();
                        usleep(100); // 5000
                    }
                    if (connection_aborted()) {
                        break;
                    }
                }
                $message = UserOpenaiChatMessage::whereId($message_id)->first();
                $chat = UserOpenaiChat::whereId($conver_id)->first();
                $message->response = $responsedText;
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->save();

                $driver->input($responsedText)->calculateCredit()->decreaseCredit();
                Usage::getSingle()->updateWordCounts($driver->calculate());
                $chat->total_credits += $total_used_tokens;
                $chat->save();
                echo 'data: [DONE]';
                echo "\n\n";
                ob_flush();
                flush();
                usleep(50000);
            }, 200, [
                'Cache-Control'     => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type'      => 'text/event-stream',
            ]);
        }

        $chat = UserOpenaiChat::where('id', $request->conver_id)->first(); // conversation
        $user = Auth::user();
        $prompt = $request->prompt;
        $total_used_tokens = 0;
        $entry = new UserOpenaiChatMessage;
        $entry->user_id = $request->user()->id;
        $entry->user_openai_chat_id = $chat->id;
        $entry->input = $prompt;
        $entry->response = null;
        $entry->output = "(If you encounter this message, please attempt to send your message again. If the error persists beyond multiple attempts, please don't hesitate to contact us for assistance!)";
        $entry->hash = Str::random(256);
        $entry->credits = $total_used_tokens;
        $entry->words = 0;
        $entry->save();
        $driver->input('')->calculateCredit()->decreaseCredit();
        $chat->total_credits += $total_used_tokens;
        $chat->save();
        $conver_id = $chat->id;
        $message_id = $entry->id;

        return response()->json(compact('conver_id', 'message_id'));
    }

    // messages under (/chat) ----------------------------------------------------------
    /**
     * Get OpenAI conversation by conver_id.
     *
     * @OA\Get(
     *      path="/api/aichat/chat/{conver_id}",
     *      operationId="conversations",
     *      tags={"AI Chats (Messages)"},
     *      security={{ "passport": {} }},
     *      summary="Get OpenAI conversation details (relate category, related conversation, related chat_completions)",
     *
     *      @OA\Parameter(
     *          name="conver_id",
     *          in="path",
     *          description="conver_id of the OpenAI chat conversations",
     *          required=true,
     *
     *          @OA\Schema(type="string", example="31"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found",
     *      ),
     * )
     */
    public function conversations(Request $request, $conver_id)
    {
        $conversation = UserOpenaiChat::where('user_id', $request->user()->id)->where('id', $conver_id)->first();

        $chat_completions = null;
        $category = null;

        if ($conversation != null) {

            $category = OpenaiGeneratorChatCategory::where('id', $conversation->openai_chat_category_id)->first();
            $chat_completions = str_replace(["\r", "\n"], '', $category->chat_completions) ?? null;

            if ($chat_completions != null) {
                $chat_completions = json_decode($chat_completions, true);
            }
        }

        return response()->json([
            'category'         => $category,
            'conversation'     => $conversation,
            'chat_completions' => $chat_completions,
        ], 200);
    }

    /**
     * Get OpenAI messages by conver_id.
     *
     * @OA\Get(
     *      path="/api/aichat/chat/{conver_id}/messages/{id}",
     *      operationId="conversationChats",
     *      tags={"AI Chats (Messages)"},
     *      security={{ "passport": {} }},
     *      summary="Get OpenAI chat details (relate category, related conversation, related messages, related chat_completions).",
     *      description="Get OpenAI chat details (relate category, related conversation, related messages, related chat_completions). ID parameter is optional. To get all messages end path with /messages",
     *
     *      @OA\Parameter(
     *          name="conver_id",
     *          in="path",
     *          description="conver_id of the OpenAI chat conversations",
     *          required=true,
     *
     *          @OA\Schema(type="string", example="31"),
     *      ),
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="id of the OpenAI chat message. Use to get one message only",
     *          required=false,
     *
     *          @OA\Schema(type="string", example="0"),
     *      ),
     *
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number",
     *
     *          @OA\Schema(type="integer", default=1),
     *      ),
     *
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Items per page",
     *
     *          @OA\Schema(type="integer", default=10),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found",
     *      ),
     * )
     */
    public function conversationChats(Request $request, $conver_id, $id = 0)
    {
        $conversation = UserOpenaiChat::where('user_id', $request->user()->id)->where('id', $conver_id)->first();
        $messages = [];

        if ($conversation != null) {
            if ($id == 0) {
                $messagesQuery = $conversation->messages()->orderBy('created_at', 'desc');
                $messages = $messagesQuery->paginate($request->input('per_page', 10));
            } else {
                $messages = UserOpenaiChatMessage::whereId($id)->first();
            }
        }

        return response()->json($messages, 200);
    }

    // other helpers under (/general) ---------------------------------------------------
    /**
     * Get the last 6 recent documents for the authenticated user.
     *
     * @OA\Get(
     *      path="/api/general/recent-documents",
     *      operationId="getRecentDocuments",
     *      tags={"General (Helpers)"},
     *      security={{ "passport": {} }},
     *      summary="Get the last 6 recent documents",
     *      description="Get the last 6 recent documents for the authenticated user, excluding documents of type 'image'.",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *  )
     */
    public function getRecentDocuments(Request $request)
    {
        $documents = $request->user()
            ->openai()
            ->whereHas('generator', function ($query) {
                $query->where('type', '!=', 'image');
            })
            ->latest('created_at')
            ->take(10)
            ->get();

        return response()->json(['data' => $documents], 200);
    }

    /**
     * Get the last 6 favorite OpenAI items for the authenticated user.
     *
     * @OA\Get(
     *      path="/api/general/favorite-openai",
     *      operationId="openAIFavoritesList",
     *      tags={"General (Helpers)"},
     *      security={{ "passport": {} }},
     *      summary="Get last 6 favorite OpenAI items",
     *      description="Get the last 6 favorite OpenAI items for the authenticated user.",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="array",
     *
     *              @OA\Items(
     *                  type="object",
     *
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="title", type="string", example="Post Title Generator"),
     *                  @OA\Property(property="description", type="string", example="Get captivating post titles instantly with our title generator. Boost engagement and save time."),
     *                  @OA\Property(property="slug", type="string", example="post_title_generator"),
     *                  @OA\Property(property="active", type="integer", example=1),
     *                  @OA\Property(property="questions", type="string", example=""),
     *                  @OA\Property(property="image", type="string", example=""),
     *                  @OA\Property(property="premium", type="integer", example=0),
     *                  @OA\Property(property="type", type="string", example="text"),
     *                  @OA\Property(property="created_at", type="string", example="2023-03-11T08:26:49.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-03-11T08:26:49.000000Z"),
     *                  @OA\Property(property="prompt", type="string", nullable=true),
     *                  @OA\Property(property="custom_template", type="integer", example=0),
     *                  @OA\Property(property="tone_of_voice", type="integer", example=0),
     *                  @OA\Property(property="color", type="string", example="#A3D6C2"),
     *                  @OA\Property(property="filters", type="string", example="blog"),
     *                  @OA\Property(property="package", type="string", nullable=true),
     *                  @OA\Property(
     *                      property="pivot",
     *                      type="object",
     *                      @OA\Property(property="user_id", type="integer", example=1),
     *                      @OA\Property(property="openai_id", type="integer", example=1),
     *                  ),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *  )
     */
    public function openAIFavoritesList(Request $request): JsonResponse
    {
        $favoriteOpenai = $request->user()->favoriteOpenai()->latest('created_at')->take(6)->get();

        return response()->json($favoriteOpenai, 200);
    }

    /**
     * Search for templates, workbooks, and AI chats based on the provided keyword.
     *
     * @OA\Post(
     *      path="/api/general/search",
     *      operationId="search",
     *      tags={"General (Helpers)"},
     *      security={{ "passport": {} }},
     *      summary="Search for templates, workbooks, and AI chats",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Keyword for the search",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="search", type="string", example="keyword"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="template_search", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="workbook_search", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="ai_chat_search", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="result", type="string"),
     *          ),
     *      ),
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $search = '';
        $result = '';
        if ($request->search !== null) {
            $search = $request->search;
        }

        if ($search === 'delete') {
            $template_search = []; // AIWriter
            $workbook_search = []; // Documents
            $ai_chat_search = []; // AI Chat
            $ai_chat_history_search = []; // AI Chat History
        } else {

            $workbook_query = [
                ['user_openai.user_id', '=', $request->user()->id],
                ['openai.type', '!=', 'image'],
            ];

            $workbook_search = UserOpenai::select('user_openai.*', 'openai.title as ai_title', 'openai.image as ai_image', 'openai.color as ai_color')
                ->join('openai', 'openai.id', '=', 'user_openai.openai_id')
                ->where($workbook_query)
                ->where(function ($query) use ($search) {
                    $query->where('user_openai.output', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_openai.title', 'LIKE', '%' . $search . '%');
                })->get();

            $template_search = OpenAIGenerator::where('title', 'like', '%' . $search . '%')->get();
            // $workbook_search = UserOpenai::where('title', 'like', "%".$word."%")->get();
            $ai_chat_search = OpenaiGeneratorChatCategory::where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')->get();

            $ai_chat_history_search = UserOpenaiChat::where('title', 'like', '%' . $search . '%')->get();

            if (count($template_search) === 0 && count($workbook_search) === 0 && count($ai_chat_search) === 0 && count($ai_chat_history_search) === 0) {
                $result = 'null';
            }
        }

        return response()->json([
            'template_search'        => $template_search,
            'workbook_search'        => $workbook_search,
            'ai_chat_search'         => $ai_chat_search,
            'ai_chat_history_search' => $ai_chat_history_search,
            'result'                 => $result,
        ], 200);
    }

    public function developerStreamTest(): StreamedResponse
    {

        return response()->stream(function () {

            $streamText400 = 'Crafting unique spaces is an art form that balances aesthetics and practicality. As an interior designer, I specialize in translating your vision into personalized designs that reflect your style. By selecting colors, furniture, and decor elements thoughtfully, we can transform your space into a meaningful reflection of your personality and preferences.';

            $words = explode(' ', $streamText400);

            foreach ($words as $response) {
                echo $response . ' ';
                ob_flush();
                flush();
                usleep(50000);

                if (connection_aborted()) {
                    break;
                }
            }
        }, 200, [
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type'      => 'text/event-stream',
        ]);
    }

    public function changeChatTitle(Request $request): JsonResponse
    {
        if ($request->streamed_message_id === null) {
            return response()->json(['error' => __('ID required')], 412);
        }

        $changed = false;
        $streamed_message_id = $request->streamed_message_id;
        $message = UserOpenaiChatMessage::whereId($streamed_message_id)->first();
        $chat_id = $message->user_openai_chat_id;
        $chat = UserOpenaiChat::whereId($chat_id)->first();

        $newTitle = '';
        if ($chat->messages()->count() <= 2) {
            $generatedNewChatTitle = OpenAI::chat()->create([
                'model'    => $this->settings->openai_default_model,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'You are a chatbot. Generate a title for a chat based on provided conversation. You must return a title only.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => "Generate a title for a chat based on the following conversation: \n\n\n\n\n"
                        . 'User Input: ' . $message->input . "\n\n\n\n\n"
                        . 'Assistant Response: ' . $message->response,
                    ],
                ],
            ]);
            $newTitle = $generatedNewChatTitle['choices'][0]['message']['content'];
            $chat->title = $newTitle;
            $chat->save();
            $changed = true;
        }

        return response()->json(['chat_id' => $chat_id, 'changed' => $changed, 'new_title' => $newTitle]);
    }
}
