<?php

namespace App\Http\Controllers\Api;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Helpers\Classes\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\OpenAIGenerator;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\UserFavorite;
use App\Models\UserOpenai;
use App\Services\Bedrock\BedrockRuntimeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AIWriterController extends Controller
{
    protected BedrockRuntimeService $bedrockService;

    protected $client;

    protected $settings;

    protected $settings_two;

    public function __construct(BedrockRuntimeService $bedrockService)
    {
        // Settings
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
        set_time_limit(120);

        $this->bedrockService = $bedrockService;
        $this->middleware(function (Request $request, $next) {
            ApiHelper::setOpenAiKey();

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     *      path="/api/aiwriter/generator/{slug}",
     *      operationId="openAIGeneratorApi",
     *      tags={"AI Writer"},
     *      security={{ "passport": {} }},
     *      summary="returns the openai writer info and related user generated docs",
     *      description="Retrieve details of an OpenAI generator by slug",
     *
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug of the OpenAI generator",
     *          required=true,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="openai", type="object"),
     *              @OA\Property(property="userOpenai", type="object"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="OpenAI generator not found",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="error", type="string", example="Resource not found"),
     *          ),
     *      ),
     * )
     */
    public function openAIGeneratorApi($slug)
    {
        try {

            $openai = OpenAIGenerator::whereSlug($slug)->firstOrFail();
            $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $openai->id)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'openai'     => $openai,
                'userOpenai' => $userOpenai,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => __('Resource not found')], 404);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/aiwriter/generator/{slug}/workbook",
     *      operationId="openAIGeneratorWorkbookApi",
     *      tags={"AI Writer"},
     *      security={{ "passport": {} }},
     *      summary="returns the openai writer info ",
     *      description="Retrieve details of an OpenAI generator workbook by slug",
     *
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug of the OpenAI generator",
     *          required=true,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="openai", type="object"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="OpenAI generator not found",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="error", type="string", example="Resource not found"),
     *          ),
     *      ),
     * )
     */
    public function openAIGeneratorWorkbookApi($slug)
    {
        try {
            $openai = OpenAIGenerator::whereSlug($slug)->firstOrFail();

            return response()->json([
                'openai' => $openai,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => __('Resource not found')], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/aiwriter/generate-output",
     *     summary="Streaming Text Output",
     *     description="Handle streamed text output based on specified parameters.",
     *     tags={"AI Writer"},
     *     security={{ "passport": {} }},
     *
     *     @OA\Parameter(
     *         name="message_id",
     *         in="query",
     *         required=true,
     *         description="ID of the message",
     *
     *         @OA\Schema(type="integer", example=1),
     *     ),
     *
     *     @OA\Parameter(
     *         name="creativity",
     *         in="query",
     *         required=true,
     *         description="Creativity level for the generated content (0 to 1)",
     *
     *         @OA\Schema(type="integer", example=1),
     *     ),
     *
     *     @OA\Parameter(
     *         name="maximum_length",
     *         in="query",
     *         required=true,
     *         description="Maximum length of the generated text",
     *
     *         @OA\Schema(type="integer", example=500),
     *     ),
     *
     *     @OA\Parameter(
     *         name="number_of_results",
     *         in="query",
     *         required=true,
     *         description="Number of summary results to generate",
     *
     *         @OA\Schema(type="integer", example=1),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="DONE"),
     *         ),
     *     ),
     *
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Error response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Error from API call. Please try again."),
     *         ),
     *     ),
     * )
     *
     * @throws Exception
     */
    public function streamedTextOutput(Request $request): StreamedResponse|JsonResponse
    {
        if ($request->message_id === null) {
            return response()->json(['error' => __('Message ID is required')], 412);
        }
        if ($request->creativity === null) {
            return response()->json(['error' => __('Creativity is required')], 412);
        }
        if ($request->maximum_length === null) {
            return response()->json(['error' => __('Max length is required')], 412);
        }
        if ($request->number_of_results === null) {
            return response()->json(['error' => __('Number of results is required')], 412);
        }

        $driver = Entity::driver();
        $driver->redirectIfNoCreditBalance();

        $settings = $this->settings;
        $message_id = $request->message_id;
        $message = UserOpenai::whereId($message_id)->first();
        $prompt = $message->input;
        $youtube_url = $request->youtube_url;
        $rss_image = $request->rss_image;
        $creativity = $request->creativity;
        $maximum_length = $request->maximum_length;
        $number_of_results = $request->number_of_results;

        return Response::stream(function () use ($driver, $prompt, $message_id, $creativity, $maximum_length, $number_of_results, $youtube_url, $rss_image) {
            try {
                if ($driver->enum() === EntityEnum::TEXT_DAVINCI_003) {
                    $stream = FacadesOpenAI::completions()->createStreamed([
                        'model'       => $driver->enum()->value,
                        'prompt'      => $prompt,
                        'temperature' => (int) $creativity,
                        'max_tokens'  => (int) $maximum_length,
                        'n'           => (int) $number_of_results,
                    ]);
                } else {
                    if ((int) $number_of_results > 1) {
                        $prompt .= ' number of results should be ' . (int) $number_of_results;
                    }
                    $stream = FacadesOpenAI::chat()->createStreamed([
                        'model'    => $driver->enum()->value,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                        ],
                    ]);
                }
            } catch (Exception $exception) {
                $messageError = 'Error from API call. Please try again. If error persists again, please contact the system administrator with this message ' . $exception->getMessage();
                Log::error($messageError);
                echo "error: $messageError";  // defined error: to handle error in frontend
                echo "\n\n";
                flush();
                echo 'data: [DONE]';
                echo "\n\n";
                flush();
                usleep(50000);
                $stream = []; // empty array to prevent further processing and $stream is undefined error
            }

            $total_used_tokens = 0;
            $output = '';
            $responsedText = '';
            // Youtube Thumbnail
            if ($youtube_url) {
                $parsedUrl = parse_url($youtube_url);
                if (isset($parsedUrl['query'])) {
                    parse_str($parsedUrl['query'], $queryParameters);
                    if (isset($queryParameters['v'])) {
                        $video_id = $queryParameters['v'];
                    }
                }
                $video_thumbnail = sprintf('https://img.youtube.com/vi/%s/maxresdefault.jpg', $video_id);
                $contents = file_get_contents($video_thumbnail);
                $nameOfImage = "youtube-$video_id.jpg";
                // save file on local storage or aws s3
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = '/uploads/' . $nameOfImage;
                $uploadedFile = new File(substr($path, 1));
                if ($this->settings_two->ai_image_storage === 's3') {
                    try {
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink(substr($path, 1));
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }

                $output = "<img src=\"$path\" style=\"width:100%\"><br><br>";

                $total_used_tokens += 1;
                $needChars = 6000 - 1;
                echo $output;
                // ob_flush();
                flush();
                usleep(500);
            }

            // RSS Thumbnail
            if ($rss_image) {
                $contents = file_get_contents($rss_image);
                $nameOfImage = 'rss-' . Str::random(12) . '.jpg';
                // save file on local storage or aws s3
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = '/uploads/' . $nameOfImage;
                $uploadedFile = new File(substr($path, 1));
                if ($this->settings_two->ai_image_storage === 's3') {
                    try {
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink(substr($path, 1));
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }
                $output = "<img src=\"$path\" style=\"width:100%\"><br><br>";
                $total_used_tokens += 1;
                $needChars = 6000 - 1;
                echo $output;
                // ob_flush();
                flush();
                usleep(500);
            }
            // Disable output buffering
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            ob_start();
            foreach ($stream as $response) {
                if ($driver->enum() === EntityEnum::TEXT_DAVINCI_003) {
                    if (isset($response->choices[0]->text)) {
                        $message = $response->choices[0]->text;
                        $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += countWords($messageFix);
                        echo $messageFix;
                        echo PHP_EOL;
                        ob_flush();
                        flush();
                        usleep(100); // 500
                    }
                } else {
                    if (isset($response['choices'][0]['delta']['content'])) {
                        $message = $response['choices'][0]['delta']['content'];
                        $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += countWords($messageFix);
                        echo $messageFix;
                        echo PHP_EOL;
                        ob_flush();
                        flush();
                        usleep(100); // 500
                    }
                }

                if (connection_aborted()) {
                    break;
                }
            }

            $message = UserOpenai::whereId($message_id)->first();
            $message->response = $responsedText;
            $message->output = $output;
            $message->hash = Str::random(256);
            $message->credits = $total_used_tokens;
            $message->words = 0;
            $message->save();
            $driver->input($response)->calculateCredit()->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());
            // echo 'data: {"status": "DONE"}';
            echo "\n\n";
            echo 'data: [DONE]';
            ob_flush();
            flush();
            usleep(50000);
        }, 200, [
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type'      => 'text/event-stream',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/aiwriter/generate/lazyload",
     *     summary="Lazy Load Images",
     *     description="Load images based on the specified parameters.",
     *     tags={"AI Writer"},
     *     security={{ "passport": {} }},
     *
     *     @OA\Parameter(
     *         name="post_type",
     *         in="query",
     *         required=true,
     *         description="Type of post",
     *
     *         @OA\Schema(
     *             type="string",
     *             enum={"ai_image_generator"},
     *             example="ai_image_generator"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         required=false,
     *         description="Offset for lazy loading",
     *
     *         @OA\Schema(
     *             type="integer",
     *             example=0
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="images", type="array", description="Array of images", @OA\Items(type="string")),
     *             @OA\Property(property="hasMore", type="boolean", description="Indicates whether there are more images")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", description="Error message")
     *         )
     *     )
     * )
     */
    public function lazyLoadImage(Request $request)
    {
        $offset = $request->get('offset', 0);
        $post_type = $request->get('post_type');
        $post = OpenAIGenerator::where('slug', $post_type)->first();

        if (! $post) {
            return response()->json([
                'error' => __('Post not found'),
            ], 404);
        }

        $limit = 5;
        $images = UserOpenai::where('user_id', Auth::id())
            ->where('openai_id', $post->id)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json([
            'images'  => $images,
            'hasMore' => $images->count() === $limit,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/aiwriter/generate/save",
     *     summary="Low Generate Save",
     *     description="Save the generated response and update user information.",
     *     tags={"AI Writer"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="message_id",
     *                     description="ID of the message",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="response",
     *                     description="Generated response",
     *                     type="string",
     *                     example="This is a generated response."
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="Data saved successfully."),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Error response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Error saving data."),
     *         ),
     *     ),
     *     security={{ "passport": {} }},
     * )
     *
     * @throws Exception
     */
    public function lowGenerateSave(Request $request)
    {
        $response = $request->response;
        $total_user_tokens = countWords($response);
        $entry = UserOpenai::find($request->message_id);

        $entry->credits = $total_user_tokens;
        $entry->words = $total_user_tokens;
        $entry->response = $response;
        $entry->output = $response;
        $entry->save();
        $driver = Entity::driver();
        $driver->redirectIfNoCreditBalance();
        $driver->input($response)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        return response()->json(['status' => 'Data saved successfully.']);
    }

    /**
     * Gets all AI Generators related to text
     *
     * @OA\Get(
     *      path="/api/aiwriter/openai-list",
     *      operationId="getOpenAIWriterList",
     *      tags={"AI Writer"},
     *      security={{ "passport": {} }},
     *      summary="Gets all AI Generators related to text",
     *      description="Gets all AI Generators related to text. Controls user access to premium generators.",
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
    public function getOpenAIWriterList(Request $request)
    {
        // Check if user can access the AI Writer premium types
        $userId = Auth::user()->id;
        $planId = '';

        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            $planId = $activeSub->plan_id;
        } else {
            $activeSub = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSub != null) {
                $planId = $activeSub->plan_id;
            }
        }

        if ($planId != '') {
            $plan = Plan::where([['id', '=', $planId]])->first();

            if ($plan->plan_type == 'All' || $plan->plan_type == 'all' || $plan->plan_type == 'premium' || $plan->plan_type == 'Premium') {
                $aiList = OpenAIGenerator::where([['type', '=', 'text']])->get();

                return response()->json($aiList, 200);
            }
        }

        $aiList = OpenAIGenerator::where([['type', '=', 'text'], ['premium', '=', false]])->get();

        return response()->json($aiList, 200);
    }

    /**
     * Gets favorite openai list
     *
     * @OA\Get(
     *      path="/api/aiwriter/favorite-openai-list",
     *      operationId="favoriteOpenaiList",
     *      tags={"AI Writer"},
     *      summary="Gets favorite openai list",
     *      description="Returns favorite openai id list of current user.",
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
    public function favoriteOpenaiList(Request $request)
    {

        $userId = Auth::user()->id;
        $favoriteList = UserFavorite::where([['user_id', '=', $userId]])->pluck('openai_id')->toArray();

        return response()->json($favoriteList, 200);

    }

    /**
     * Add favorite openai list
     *
     * @OA\Post(
     *      path="/api/aiwriter/favorite-openai-list-add",
     *      operationId="addToFavoriteOpenaiList",
     *      tags={"AI Writer"},
     *      summary="Adds openai to favorite openai list",
     *      description="Returns favorite openai id list of current user.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Parameter(
     *         name="openai_id",
     *         in="query",
     *         required=true,
     *         description="OpenAI ID",
     *
     *         @OA\Schema(
     *             type="integer",
     *             example=0
     *         )
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
    public function addToFavoriteOpenaiList(Request $request)
    {

        if ($request->openai_id == null) {
            return response()->json(['error' => __('OpenAI ID is required')], 412);
        }

        $openAi = OpenAIGenerator::where([['id', '=', $request->openai_id]])->first();
        if ($openAi == null) {
            return response()->json(['error' => __('OpenAI not found')], 412);
        }

        $userId = Auth::user()->id;

        $favorite = new UserFavorite;
        $favorite->user_id = $userId;
        $favorite->openai_id = $openAi->id;
        $favorite->save();

        $favoriteList = UserFavorite::where([['user_id', '=', $userId]])->pluck('openai_id')->toArray();

        return response()->json($favoriteList, 200);

    }

    /**
     * Remove from favorite openai list
     *
     * @OA\Post(
     *      path="/api/aiwriter/favorite-openai-list-remove",
     *      operationId="removeFromFavoriteOpenaiList",
     *      tags={"AI Writer"},
     *      summary="Removes openai from favorite openai list",
     *      description="Returns favorite openai id list of current user.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Parameter(
     *         name="openai_id",
     *         in="query",
     *         required=true,
     *         description="OpenAI ID",
     *
     *         @OA\Schema(
     *             type="integer",
     *             example=0
     *         )
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
     *          response=404,
     *          description="Not found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
     */
    public function removeFromFavoriteOpenaiList(Request $request)
    {

        if ($request->openai_id == null) {
            return response()->json(['error' => __('OpenAI ID is required')], 412);
        }

        $openAi = OpenAIGenerator::where([['id', '=', $request->openai_id]])->first();
        if ($openAi == null) {
            return response()->json(['error' => __('OpenAI not found')], 412);
        }

        $userId = Auth::user()->id;

        $isFavorite = UserFavorite::where([['user_id', '=', $userId], ['openai_id', '=', $openAi->id]])->first();
        if ($isFavorite == null) {
            return response()->json(['error' => __('OpenAI is not in your favorite list')], 404);
        }

        $isFavorite->delete();

        $favoriteList = UserFavorite::where([['user_id', '=', $userId]])->pluck('openai_id')->toArray();

        return response()->json($favoriteList, 200);

    }
}
