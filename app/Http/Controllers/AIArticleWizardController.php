<?php

namespace App\Http\Controllers;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\EntityStats;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Enums\BedrockEngine;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Models\ArticleWizard;
use App\Models\OpenAIGenerator;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\UserOpenai;
use App\Services\Bedrock\BedrockRuntimeService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JsonException;
use OpenAI\Laravel\Facades\OpenAI;
use Random\RandomException;

class AIArticleWizardController extends Controller
{
    protected BedrockRuntimeService $bedrockService;

    protected $client;

    protected $settings;

    protected $settings_two;

    public const STORAGE_S3 = 's3';

    public const STORAGE_LOCAL = 'public';

    public const CLOUDFLARE_R2 = 'r2';

    public function __construct(BedrockRuntimeService $bedrockService)
    {
        $this->bedrockService = $bedrockService;
        $this->middleware(function (Request $request, $next) {
            ApiHelper::setOpenAiKey();

            return $next($request);
        });
        // Settings
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
        ini_set('max_execution_time', 120000);
    }

    public function index()
    {
        $wizards = ArticleWizard::select('id', 'title', 'created_at', 'generated_count', 'current_step', 'id')
            ->orderBy('id', 'asc')
            ->get();

        return view('panel.user.article_wizard.list', compact('wizards'));
    }

    /**
     * Create new article and return article id | not rec
     */
    public function newArticle(Request $request)
    {
        abort_if(Helper::setting('feature_ai_article_wizard') == 0, 404);

        $user_id = Auth::id();

        $wizard = ArticleWizard::where('user_id', $user_id)->where('current_step', '!=', 4)->first();

        if (! $wizard) {

            $records = ArticleWizard::where('user_id', $user_id)->get();
            foreach ($records as $record) {
                $extraImages = json_decode($record->extra_images, true);
                if ($extraImages != null) {
                    foreach ($extraImages as $extraImage) {
                        if (json_decode($record->image) != $extraImage['path']) {
                            if (($extraImage['storage'] ?? '') == self::STORAGE_S3) {
                                Storage::disk(self::STORAGE_S3)->delete(basename($extraImage['path']));
                            } else {
                                if (file_exists(substr($extraImage['path'], 1))) {
                                    unlink(substr($extraImage['path'], 1));
                                }
                            }
                        }
                    }
                }
            }

            ArticleWizard::where('user_id', $user_id)->delete();

            $wizard = new ArticleWizard;
            $wizard->user_id = $user_id;
            $wizard->current_step = 0;
            $wizard->keywords = '';
            $wizard->extra_keywords = '';
            $wizard->topic_keywords = '';
            $wizard->title = '';
            $wizard->extra_titles = '';
            $wizard->topic_title = '';
            $wizard->outline = '';
            $wizard->extra_outlines = '';
            $wizard->topic_outline = '';
            $wizard->result = '';
            $wizard->image = '';
            $wizard->extra_images = '';
            $wizard->topic_image = '';
            $wizard->save();
        }

        $wizard = ArticleWizard::find($wizard->id);
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
        if ($this->settings_two->openai_default_stream_server == 'backend') {
            $apikeyPart1 = base64_encode(rand(1, 100));
            $apikeyPart2 = base64_encode(rand(1, 100));
            $apikeyPart3 = base64_encode(rand(1, 100));
        } else {
            $apiKey = ApiHelper::setOpenAiKey();

            $len = strlen($apiKey);
            $len = max($len, 6);
            $parts[] = substr($apiKey, 0, $l[] = rand(1, $len - 5));
            $parts[] = substr($apiKey, $l[0], $l[] = rand(1, $len - $l[0] - 3));
            $parts[] = substr($apiKey, array_sum($l));
            $apikeyPart1 = base64_encode($parts[0]);
            $apikeyPart2 = base64_encode($parts[1]);
            $apikeyPart3 = base64_encode($parts[2]);
        }

        return view('panel.user.article_wizard.wizard', compact(
            'wizard',
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'apiUrl'
        ));
    }

    // | not rec
    public function clearArticle(Request $request)
    {
        $user_id = Auth::id();
        $records = ArticleWizard::where('user_id', $user_id)->get();
        foreach ($records as $record) {
            $extraImages = json_decode($record->extra_images, true);
            if ($extraImages != null) {
                foreach ($extraImages as $extraImage) {
                    if ($record->image != $extraImage['path']) {
                        if (($extraImage['storage'] ?? '') == self::STORAGE_S3) {
                            Storage::disk(self::STORAGE_S3)->delete(basename($extraImage['path']));
                        } else {
                            Storage::disk(self::STORAGE_LOCAL)->delete(substr($extraImage['path'], 1));
                        }
                    }
                }
            }
        }
        ArticleWizard::where('user_id', Auth::id())->delete();

        return response()->json('success');
    }

    /** # | not rec
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $wizard = ArticleWizard::find($id);

        return view('panel.user.article_wizard.wizard', compact('wizard'));
    }

    // | not rec
    public function editArticle(string $id)
    {
        $wizard = ArticleWizard::find($id);

        return view('panel.user.article_wizard.wizard', compact('wizard'));
    }

    /** # | not rec
     * Generate keywords from topic
     */
    public function userRemaining(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['words' =>  EntityStats::word()->totalCredits(), 'images' =>  EntityStats::image()->totalCredits()]);
    }

    // | not rec
    public function generateKeywords(Request $request)
    {
        try {
            // @todo will be changed.
            $chatBot = $this->getDefaultOpenAiWordModel();
            $driver = Entity::driver($chatBot);
            $driver->redirectIfNoCreditBalance();

            $completion = OpenAI::chat()->create([
                'model'    => $chatBot?->value,
                'messages' => [[
                    'role'    => 'user',
                    'content' => "Generate $request->count keywords(simple words or 2 words, not phrase, not person name) about '$request->topic'. Must resut as array json data. in '$request->language' language. Result format is [keyword1, keyword2, ..., keywordn].  Must not write ```json",
                ]],
            ]);

            $responsedText = $completion['choices'][0]['message']['content'];
            $driver->input($responsedText)->calculateCredit()->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());

            return response()->json(['result' => $responsedText])->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // | not rec
    public function generateTitles(Request $request)
    {
        try {
            $defaultModel = $this->getDefaultOpenAiWordModel();
            $driver = Entity::driver($defaultModel);
            $driver->redirectIfNoCreditBalance();

            $prompt = "Generate $request->count titles(Maximum title length is $request->length. Must not be 'title1', 'title2', 'title3', 'title4', 'title5') about Keywords: '" . $request->keywords . "'. in '$request->language' language. Resut must be array json data. This is result format: [title1, title2, ..., titlen]. Maximum title length is $request->length  Must not write ```json";
            if ($request->topic != '') {
                $prompt = "Generate $request->count titles(Maximum title length is $request->length., Must not be 'title1', 'title2', 'title3', 'title4', 'title5') about Topic: '" . $request->topic . "'. in '$request->language' language. Resut must be array json data. This is result format: [title1, title2, ..., titlen]. Maximum title length is $request->length  Must not write ```json";
            }
            $completion = OpenAI::chat()->create([
                'model'    => $defaultModel?->value,
                'messages' => [[
                    'role'    => 'user',
                    'content' => $prompt,
                ]],
            ]);
            $responsedText = $completion['choices'][0]['message']['content'];
            $driver->input($responsedText)
                ->calculateCredit()
                ->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());

            return response()->json(['result' => $responsedText])->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // | not rec
    public function generateOutlines(Request $request)
    {
        try {
            $prompt = "The keywords of article are $request->keywords.  Generate different outlines( Each outline must has only $request->subcount subtitles(Without number for order, subtitles are not keywords)) $request->count times. The depth is 1. in '$request->language' language. Must not write any description. Result must be json data, Every subtitle is sentence or phrase string. This is result format: [[subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->subcount(string)], [subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->subcount(string)], ... ,[subtitle1(string), subtitle2(string), subtitle3(string), ..., subtitle-$request->subcount (string)].  Must not write ```json";
            if ($request->topic !== '') {
                $prompt = "The subject of article is $request->topic. Generate different outlines( Each outline must has only $request->subcount subtitles(Without number for order, subtitles are not keywords)) $request->count times. The depth is 1" . ". in '$request->language' language. Must not write any description. Result must be json data, Every subtitle is sentence or phrase string. This is result format: [[subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->subcount(string)], [subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->subcount(string)], ... ,[subtitle1(string), subtitle2(string), subtitle3(string), ..., subtitle-$request->subcount (string)]].  Must not write ```json";
            }
            $chatBot = $this->getDefaultOpenAiWordModel();
            $driver = Entity::driver($chatBot);
            $driver->redirectIfNoCreditBalance();
            $completion = OpenAI::chat()->create([
                'model'    => $chatBot?->value,
                'messages' => [[
                    'role'    => 'user',
                    'content' => $prompt,
                ]],
            ]);

            $responsedText = $completion['choices'][0]['message']['content'];
            $driver->input($responsedText)
                ->calculateCredit()
                ->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());

            return response()->json(['result' => $responsedText, 'words' =>  EntityStats::word()->totalCredits(), 'images' =>  EntityStats::image()->totalCredits()])->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // | not rec
    public function generateArticle(Request $request)
    {
        try {
            $wizard = ArticleWizard::find($request->id);
            $title = $wizard->title;
            $keywords = $wizard->keywords;
            $outlines = json_decode($wizard->outline, true);
            $length = $request->length;
            session_start();
            header('Content-type: text/event-stream');
            header('Cache-Control: no-cache');
            $chatBot = $this->getDefaultOpenAiWordModel();
            Entity::driver($chatBot)
                ->redirectIfNoCreditBalance();
            $result = OpenAI::chat()->createStreamed([
                'model'    => $chatBot?->value,
                'messages' => [[
                    'role'    => 'user',
                    'content' => "Write Article(Maximum  $length words). in $wizard-> language. Generate article (Must not contain title, Must Mark outline with <h3> tag) about $title with following outline " . implode(',', $outlines) . 'Must mark outline with <h3> tag.  Must not write ```json',
                ]],
                'stream' => true,
            ]);

            foreach ($result as $response) {
                echo "event: data\n";
                echo 'data: ' . json_encode(['message' => $response->choices[0]->delta->content]) . "\n\n";
                flush();
            }

            echo "event: stop\n";
            echo "data: stopped\n\n";
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // | not rec
    public function generateImages(Request $request)
    {
        $defaultEngine = setting('default_aw_image_engine', EngineEnum::UNSPLASH->value);
        $defaultModel = $this->getDefaultImageModelFromImageEngine($defaultEngine);

        try {
            $wizard = ArticleWizard::find($request->id);
            $size = $request->size;
            $prompt = $request->prompt;
            if (empty($prompt)) {
                $prompt = $wizard->topic_keywords;
            }
            $count = $request->count;
            $paths = [];
            $this->checkBalanceForImages($defaultModel, $count);
            $this->getImagesFromThirdParty($defaultEngine, $defaultModel, $prompt, $count, $size, $paths);

            return response()->json(['status' => 'success', 'path' => $paths]);
        } catch (ClientException|Exception|GuzzleException $e) {
            $engine = $defaultEngine === 'sd' ? EngineEnum::STABLE_DIFFUSION->slug() : $defaultEngine;

            if (! ($e instanceof Exception) && $e?->getResponse()?->getStatusCode() === 401) {
                // Unauthorized error
                if (Auth::user()?->isAdmin()) {
                    return response()->json([
                        'message' => __('It seems your :label API key is missing or invalid. Please go to your settings and add a valid :label API key.', ['label' => EngineEnum::fromSlug($engine)->label()]),
                    ], 401);
                }

                return response()->json([
                    'message' => __('It seems that :label API not set yet or is missing or invalid. Please submit a ticket to support.', ['label' =>  EngineEnum::fromSlug($engine)->label()]),
                ], 401);
            }

            return response()->json([
                'message' => $e->getMessage() . "Model: $defaultModel->value",
            ], 500);
        }
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws RandomException
     */
    private function getImagesFromThirdParty($defaultEngine, $defaultModel, $prompt, $count, $size, &$paths): void
    {
        match ($defaultEngine) {
            EngineEnum::PEXELS->value                     => $this->getImagesFromPexels($defaultModel, $prompt, $count, $size, $paths),
            EngineEnum::PIXABAY->value                    => $this->getImagesFromPixabay($defaultModel, $prompt, $count, $paths),
            EngineEnum::OPEN_AI->value                    => $this->getImagesDalle($defaultModel, $prompt, $count, $size, $paths),
            'sd', EngineEnum::STABLE_DIFFUSION->value     => $this->getImagesFromStableDiffusion($defaultModel, $prompt, $count, $size, $paths),
            default                                       => $this->getImagesFromUnsplash($defaultModel, $prompt, $count, $size, $paths),
        };
    }

    /**
     * @throws Exception
     */
    private function checkBalanceForImages(EntityEnum $defaultModel, int $count): void
    {
        Entity::driver($defaultModel)->inputImageCount($count)->calculateCredit()->redirectIfNoCreditBalance();
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws Exception
     */
    private function getImagesFromPexels($defaultModel, $prompt, $count, $size, &$paths)
    {
        $driver = Entity::driver($defaultModel)->inputImageCount($count)->calculateCredit();
        $driver->redirectIfNoCreditBalance();
        $size = match ($size) {
            'thumb'    => 'tiny',
            'small'    => 'small',
            'full'     => 'large',
            'raw'      => 'original',
            default    => 'medium',
        };
        $image_storage = $this->settings_two->ai_image_storage;
        $client = new Client;
        $apiKey = setting('pexels_api_key');
        $url = "https://api.pexels.com/v1/search?query=$prompt&per_page=$count";
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => $apiKey,
            ],
        ]);
        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        if ($statusCode === 200) {
            $images = json_decode($content, false, 512, JSON_THROW_ON_ERROR)->photos;
            foreach ($images as $image) {
                $imageContent = file_get_contents($image->src->$size);
                $nameOfImage = Str::random(12) . '.png';
                Storage::disk('public')->put($nameOfImage, $imageContent);
                $path = 'uploads/' . $nameOfImage;
                if ($image_storage === self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File($path);
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink($path);
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                } else {
                    $path = "/$path";
                }
                $paths[] = $path;
                $count--;
                if ($count === 0) {
                    break;
                }
            }
            Usage::getSingle()->updateImageCounts($driver->calculate());
            $driver->decreaseCredit();
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => __('Failed to download images.'),
            ], 500);
        }
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws Exception
     */
    private function getImagesFromPixabay($defaultModel, $prompt, $count, &$paths)
    {
        $driver = Entity::driver($defaultModel)->inputImageCount($count)->calculateCredit();
        $driver->redirectIfNoCreditBalance();

        $image_storage = $this->settings_two->ai_image_storage;
        $client = new Client;
        $apiKey = setting('pixabay_api_key');
        $url = "https://pixabay.com/api/?key=$apiKey&q=$prompt&image_type=photo&per_page=$count";
        $response = $client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        if ($statusCode == 200) {
            $images = json_decode($content, false, 512, JSON_THROW_ON_ERROR)->hits;
            foreach ($images as $image) {
                $image_url = $image->webformatURL;
                $imageContent = file_get_contents($image_url);
                $nameOfImage = Str::random(12) . '.png';

                Storage::disk('public')->put($nameOfImage, $imageContent);
                $path = 'uploads/' . $nameOfImage;
                if ($image_storage === self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File($path);
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink($path);
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                } else {
                    $path = "/$path";
                }
                $paths[] = $path;
                $count--;
                if ($count === 0) {
                    break;
                }
            }
            Usage::getSingle()->updateImageCounts($driver->calculate());
            $driver->decreaseCredit();
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => __('Failed to download images.'),
            ], 500);
        }
    }

    private function getImagesDalle($defaultModel, $prompt, $count, $size, &$paths)
    {
        $driver = Entity::driver($defaultModel)->inputImageCount($count)->calculateCredit();
        $driver->redirectIfNoCreditBalance();

        $count = 1;
        $image_storage = $this->settings_two->ai_image_storage;

        $lockKey = 'generate_image_lock';
        // Attempt to acquire lock
        if (! Cache::lock($lockKey, 10)->get()) {
            // Failed to acquire lock, another process is already running
            return response()->json(['message' => 'Image generation in progress. Please try again later.'], 409);
        }

        try {
            // check daily limit
            $chkLmt = Helper::checkImageDailyLimit();
            if ($chkLmt->getStatusCode() === 429) {
                return $chkLmt;
            }

            $driver->redirectIfNoCreditBalance();

            $size = match ($size) {
                'small' => $defaultModel === EntityEnum::DALL_E_3->value ? '1024x1792' : '512x512',
                'large', 'full', 'medium' => $defaultModel === EntityEnum::DALL_E_3->value ? '1792x1024' : '1024x1024',
                default => $defaultModel === EntityEnum::DALL_E_3->value ? '1024x1024' : '256x256',
            };
            for ($i = 0; $i < $count; $i++) {
                $response = OpenAI::images()->create([
                    'model'           => $defaultModel,
                    'prompt'          => $prompt,
                    'size'            => $size,
                    'response_format' => 'b64_json',
                    'quality'         => 'standard',
                    'n'               => 1,
                ]);
                $image_url = $response['data'][0]['b64_json'];
                $contents = base64_decode($image_url);

                $nameOfImage = Str::random(12) . '.png';
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = 'uploads/' . $nameOfImage;

                if ($image_storage === self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File($path);
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink($path);
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                } elseif ($image_storage === self::CLOUDFLARE_R2) {
                    Storage::disk('r2')->put($nameOfImage, $contents);
                    unlink($path);
                    $path = Storage::disk('r2')->url($nameOfImage);
                } else {
                    $path = "/$path";
                }

                $paths[] = $path;
            }
            Usage::getSingle()->updateImageCounts($driver->calculate());
            $driver->decreaseCredit();
            Cache::lock($lockKey)->release();
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } finally {
            Cache::lock($lockKey)->forceRelease();
        }
    }

    /**
     * @throws GuzzleException
     * @throws RandomException
     */
    private function getImagesFromStableDiffusion($defaultModel, $prompt, $count, $size, &$paths)
    {
        $driver = Entity::driver($defaultModel)->inputImageCount($count)->calculateCredit();
        $driver->redirectIfNoCreditBalance();

        $count = 1;
        $settings = $this->settings_two;
        $image_storage = $this->settings_two->ai_image_storage;

        $size = match ($size) {
            'thumb'  => '640x1536',
            'small'  => '768x1344',
            'medium' => '832x1216',
            'large'  => '896x1152',
            'raw'    => '1152x896',
            default  => '1024x1024',
        };

        $stablediffusionKey = $this->getStableApiKey();
        for ($i = 0; $i < $count; $i++) {
            if (empty($prompt)) {
                return response()->json(['status' => 'error', 'message' => 'You must provide a prompt']);
            }

            $width = (int) explode('x', $size)[0];
            $height = (int) explode('x', $size)[1];

            if ($settings->stablediffusion_default_model === BedrockEngine::BEDROCK->value) {
                $path = $this->bedrockService->invokeStableDiffusion(
                    $prompt, random_int(1, 1000000), $width, $height);
                $paths[] = $path;
            } else {
                if (empty($stablediffusionKey)) {
                    return response()->json(['status' => 'error', 'message' => 'You must provide a StableDiffusion API Key.']);
                }

                // Stablediffusion engine
                $engine = $this->settings_two->stablediffusion_default_model;
                $isV2BetaModels = EntityEnum::fromSlug($engine)->isV2BetaSdEntity();

                if ($isV2BetaModels) {
                    $client = new Client([
                        'base_uri' => 'https://api.stability.ai/v2beta/stable-image/generate/',
                        'headers'  => [
                            'content-type'  => 'multipart/form-data',
                            'Authorization' => 'Bearer ' . $stablediffusionKey,
                            'accept'        => 'application/json',
                        ],
                    ]);
                } else {
                    $client = new Client([
                        'base_uri' => 'https://api.stability.ai/v1/generation/',
                        'headers'  => [
                            'content-type'  => 'application/json',
                            'Authorization' => 'Bearer ' . $stablediffusionKey,
                            'accept'        => 'application/json',
                        ],
                    ]);
                }

                // Content Type
                $content_type = 'json';

                $payload = [
                    'cfg_scale'            => 7,
                    'clip_guidance_preset' => 'NONE',
                    'samples'              => 1,
                    'steps'                => 50,
                ];

                $stable_url = 'text-to-image';
                // $payload['width'] = $width;
                // $payload['height'] = $height;
                $sd3Payload = [
                    [
                        'name'     => 'prompt',
                        'contents' => $prompt,
                    ],
                    [
                        'name'     => 'file',
                        'contents' => 'no',
                    ],
                    [
                        'name'     => 'output_format',
                        'contents' => 'png',
                    ],
                ];

                $validEngines = [EntityEnum::SD_3->value, EntityEnum::SD_3_TURBO->value, EntityEnum::SD_3_MEDIUM->value, EntityEnum::SD_3_LARGE->value, EntityEnum::SD_3_LARGE_TURBO->value];
                if (in_array($engine, $validEngines, true)) {
                    $engine = 'sd3';
                    $sd3Payload[] = [
                        'name'     => 'model',
                        'contents' => $engine,
                    ];
                }
                $prompt = [
                    [
                        'text'   => $prompt,
                        'weight' => 1,
                    ],
                ];
                $payload['text_prompts'] = $prompt;

                try {
                    if ($isV2BetaModels) {
                        $response = $client->post((string) $engine, [
                            'headers' => [
                                'accept' => 'application/json',
                            ],
                            'multipart' => $sd3Payload,
                        ]);
                    } else {
                        $response = $client->post("$engine/$stable_url", [
                            $content_type => $payload,
                        ]);
                    }

                } catch (RequestException $e) {

                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $statusCode = $response->getStatusCode();
                        // Custom handling for specific status codes here...

                        if ($statusCode == '404') {
                            // Handle a not found error
                        } elseif ($statusCode == '500') {
                            // Handle a server error
                        }

                        $errorMessage = $response->getBody()->getContents();

                        return response()->json(['status' => 'error', 'message' => json_decode($errorMessage)->message]);
                        // Log the error message or handle it as required
                    }

                    return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                }

                $body = $response->getBody();
                if ($response->getStatusCode() == 200) {
                    $nameOfImage = Str::random(12) . '.png';

                    if ($isV2BetaModels) {
                        $contents = base64_decode(json_decode($body)->image);
                    } else {
                        $contents = base64_decode(json_decode($body)->artifacts[0]->base64);
                    }
                    Storage::disk('public')->put($nameOfImage, $contents);
                    $path = 'uploads/' . $nameOfImage;
                    if ($image_storage === self::STORAGE_S3) {
                        try {
                            $uploadedFile = new File($path);
                            $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                            unlink($path);
                            $path = Storage::disk('s3')->url($aws_path);
                        } catch (Exception $e) {
                            return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                        }
                    } elseif ($image_storage === self::CLOUDFLARE_R2) {
                        Storage::disk('r2')->put($nameOfImage, $contents);
                        unlink($path);
                        $path = Storage::disk('r2')->url($nameOfImage);
                    } else {
                        $path = "/$path";
                    }

                    $paths[] = $path;
                } else {
                    $message = '';
                    if ($body->status == 'error') {
                        $message = $body->message;
                    } else {
                        $message = 'Failed, Try Again';
                    }

                    return response()->json(['status' => 'error', 'message' => $message]);
                }
            }
        }
        Usage::getSingle()->updateImageCounts($driver->calculate());
        $driver->decreaseCredit();
    }

    private function getImagesFromUnsplash($defaultModel, $prompt, $count, $size, &$paths): void
    {
        $driver = Entity::driver($defaultModel)->inputImageCount($count)->calculateCredit();
        $driver->redirectIfNoCreditBalance();

        $settings = $this->settings_two;
        $image_storage = $this->settings_two->ai_image_storage;
        $client = new Client;
        $apiKey = $settings->unsplash_api_key;
        $url = "https://api.unsplash.com/search/photos?query=$prompt&count=$count&client_id=$apiKey&orientation=landscape";
        $response = $client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        if ($statusCode == 200) {
            $images = json_decode($content)->results;

            foreach ($images as $index => $image) {
                $image_url = $image->urls->$size;
                $imageContent = file_get_contents($image_url);
                $nameOfImage = Str::random(12) . '.png';

                Storage::disk('public')->put($nameOfImage, $imageContent);
                $path = 'uploads/' . $nameOfImage;

                if ($image_storage === self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File($path);
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink($path);
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);

                        return;
                    }
                } else {
                    $path = "/$path";
                }

                $paths[] = $path;
                $count--;
                if ($count === 0) {
                    break;
                }
            }
            Usage::getSingle()->updateImageCounts($driver->calculate());
            $driver->decreaseCredit();
        } else {
            response()->json([
                'status'  => 'error',
                'message' => __('Failed to download images.'),
            ], 500);
        }
    }

    // | not rec
    public function updateArticle(Request $request)
    {
        try {
            $defaultModel = $this->getDefaultOpenAiWordModel();
            $driver = Entity::driver($defaultModel);
            $driver->redirectIfNoCreditBalance();

            $data = $request->getContent();
            $decodedData = json_decode($data);

            $wizard = ArticleWizard::find($decodedData->id);
            if ($decodedData->type == 'EXTRA_KEYWORDS') {
                $wizard->extra_keywords = $decodedData->extra_keywords;
                $wizard->topic_keywords = $decodedData->topic_keywords;
            }
            if ($decodedData->type == 'EXTRA_TITLES') {
                $wizard->extra_titles = $decodedData->extra_titles;
                $wizard->topic_title = $decodedData->topic_title;
            }
            if ($decodedData->type == 'EXTRA_OUTLINES') {
                $wizard->extra_outlines = $decodedData->extra_outlines;
                $wizard->topic_outline = $decodedData->topic_outline;
            }
            if ($decodedData->type == 'EXTRA_IMAGES') {
                $wizard->extra_images = $decodedData->extra_images;
                $wizard->topic_image = $decodedData->topic_image;
            }
            if ($decodedData->type == 'KEYWORDS') {
                $wizard->keywords = $decodedData->keywords;
                $wizard->current_step = 1;
            }
            if ($decodedData->type == 'TITLE') {
                $wizard->title = $decodedData->title;
                $wizard->current_step = 2;
            }
            if ($decodedData->type == 'OUTLINE') {
                $wizard->outline = $decodedData->outline;
                $wizard->current_step = 3;
            }
            if ($decodedData->type == 'STEP') {
                $wizard->current_step = $decodedData->step;
            }
            if ($decodedData->type == 'UPDATE_STEP') {
                $wizard->current_step = $decodedData->step;
                if ($decodedData->step <= 0) {
                    $wizard->title = '';
                    $wizard->extra_titles = '';
                }
                if ($decodedData->step <= 1) {
                    $wizard->outline = '';
                    $wizard->extra_outlines = '';
                }
                if ($decodedData->step <= 2) {
                    $wizard->image = '';
                    $wizard->extra_images = '';
                }
            }
            if ($decodedData->type == 'IMAGE') {
                $wizard->image = $decodedData->image;
                $wizard->language = $decodedData?->language ?? $this->settings->openai_default_language;
                $decodedData->creativity = $decodedData?->creativity ?? $this->settings->openai_default_creativity;
                $wizard->current_step = 4;
            }

            if ($decodedData->type == 'TOKENS') {
                $total_used_tokens = $decodedData->tokens;
                $responsedText = generateRandomWords($total_used_tokens);
                $driver->input($responsedText)
                    ->calculateCredit()
                    ->decreaseCredit();
                Usage::getSingle()->updateWordCounts($driver->calculate());
            }

            if ($decodedData->type == 'RESULT') {
                $wizard->result = $decodedData->result;

                $user = Auth::user();

                $post = OpenAIGenerator::where('slug', 'ai_article_wizard_generator')->first();

                $entry = new UserOpenai;
                $entry->title = $wizard->title;
                $entry->slug = str()->random(7) . str($user?->fullName())->slug() . '-workbook';
                $entry->user_id = Auth::id();
                $entry->openai_id = $post->id;
                $entry->input = "Write Article in $wizard-> language. Generate article about $wizard->title with must following outline $request->outline.  Please write only article.";
                $entry->hash = str()->random(256);
                $entry->credits = countWords($decodedData->result);
                $entry->words = countWords($decodedData->result);
                $entry->output = $decodedData->result;
                $entry->storage = $this->settings_two->ai_image_storage;
                $entry->response = json_decode($wizard->image);

                $driver
                    ->input($decodedData->result)
                    ->calculateCredit()
                    ->decreaseCredit();
                Usage::getSingle()->updateWordCounts($driver->calculate());
                $entry->save();
            }

            $wizard->save();

            return response()->json(['result' => 'success', 'remain_words' => (string) EntityStats::word()->totalCredits(), 'remain_images' => (string) EntityStats::image()->totalCredits()]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function startover()
    {
        $user = Auth::user();
        ArticleWizard::where('user_id', $user->id)->delete();

        return response()->json(['result' => 'success']);
    }

    private function getDefaultImageModelFromImageEngine(string $defaultEngine): EntityEnum
    {
        return match ($defaultEngine) {
            EngineEnum::PEXELS->value                     => EntityEnum::PEXELS,
            EngineEnum::PIXABAY->value                    => EntityEnum::PIXABAY,
            EngineEnum::OPEN_AI->value                    => $this->getDefaultOpenAiImageModel(),
            'sd', EngineEnum::STABLE_DIFFUSION->value     => $this->getStableDiffusionDefaultModel(),
            default                                     => EntityEnum::UNSPLASH,
        };
    }

    private function getDefaultOpenAiWordModel(): EntityEnum
    {
        return EntityEnum::fromSlug($this->settings?->openai_default_model) ?? EntityEnum::GPT_4_O;
    }

    private function getDefaultOpenAiImageModel(): EntityEnum
    {
        return EntityEnum::fromSlug($this->settings_two?->dalle) ?? EntityEnum::DALL_E_2;
    }

    private function getStableDiffusionDefaultModel(): EntityEnum
    {
        return EntityEnum::fromSlug($this->settings_two?->stablediffusion_default_model) ?? EntityEnum::SD_3;
    }

    private function getStableApiKey(): string
    {
        $stableDiffusionKeys = explode(',', $this->settings_two->stable_diffusion_api_key);

        return $stableDiffusionKeys[array_rand($stableDiffusionKeys)];
    }
}
