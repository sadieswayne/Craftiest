<?php

namespace App\Http\Controllers\Api;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Services\FalAIService;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Enums\BedrockEngine;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\OpenAIGenerator;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\User;
use App\Models\UserOpenai;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;
use Random\RandomException;

class AIImageController extends Controller
{
    protected $client;

    protected $settings;

    protected $settings_two;

    public const STABLEDIFFUSION = 'stablediffusion';

    public const STORAGE_S3 = 's3';

    public const CLOUDFLARE_R2 = 'r2';

    public const LOADING_GIF = '/themes/default/assets/img/loading.svg';

    public const STORAGE_LOCAL = 'public';

    public function __construct()
    {
        // Settings
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
        ApiHelper::setOpenAiKey();
        set_time_limit(120);
    }

    /**
     * Get Model Versions for AI Image Generation
     *
     * @OA\Get(
     *      path="/api/aiimage/versions",
     *      operationId="versions",
     *      tags={"AI Image Generation"},
     *      security={{ "passport": {} }},
     *      summary="Get Model Versions for AI Image Generation (DALL-E, Stable Diffusion) from settings",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function versions(): JsonResponse
    {
        return response()->json([
            'dall-e'           => $this->settings_two->dalle,
            'stable-diffusion' => $this->settings_two->stablediffusion_default_model,
        ]);
    }

    /**
     * Check if image generation is active
     *
     * @OA\Get(
     *      path="/api/aiimage/check-availability",
     *      operationId="checkActiveGeneration",
     *      tags={"AI Image Generation"},
     *      security={{ "passport": {} }},
     *      summary="Check if image generation is active / available",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Image generation is available.",
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Image generation in progress. Please try again later.",
     *      ),
     * )
     */
    public function checkActiveGeneration(): JsonResponse
    {
        $lockKey = 'generate_image_output_lock';

        // Attempt to acquire lock
        if (! Cache::lock($lockKey, 10)->get()) {
            // Failed to acquire lock, another process is already running
            return response()->json([
                'status'  => 'error',
                'message' => 'Image generation in progress. Please try again later.',
            ], 409);
        }

        // Release the lock
        Cache::lock($lockKey)->forceRelease();

        return response()->json([
            'status'  => 'success',
            'message' => 'Image generation is available.',
        ], 200);
    }

    /**
     * Generate Image
     *
     * @OA\Post(
     *      path="/api/aiimage/generate-image",
     *      operationId="generateImage",
     *      tags={"AI Image Generation"},
     *      security={{ "passport": {} }},
     *      summary="Generate Image (DALL-E / Stable Diffusion parameters required in request)",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Image generation successful. Image info in json response [images].",
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Image generation in progress. Please try again later.",
     *      ),
     * )
     */
    public function generateImage(Request $request): JsonResponse
    {
        $imageParam = $request->all();
        $post_type = 'ai_image_generator';
        $post = OpenAIGenerator::where('slug', $post_type)->first();
        $user = Auth::user();

        return $this->imageOutput($imageParam, $post, $user);
    }

    // This functions is copied from app/Http/Controllers/AIController.php as it is
    public function imageOutput($param, $post, $user): JsonResponse
    {
        $lockKey = 'generate_image_output_lock';
        if (! Cache::lock($lockKey, 10)->get()) { // Attempt to acquire lock
            return response()->json(['message' => 'Image generation in progress. Please try again later.'], 409);
        }

        $engineCheck = match ($param['image_generator']) {
            'flux-pro' => EngineEnum::FAL_AI->value,
            'dall-e'   => EngineEnum::OPEN_AI->value,
            default    => $param['image_generator'],
        };
        $entries = [];

        try {
            $engine = EngineEnum::fromSlug($engineCheck);
            $model = $this->getDefaultModel($engine);
            $code = $this->getEngineCode($engine);
            $number_of_images = (int) $param['image_number_of_images'];

            $driver = Entity::driver($model)->inputImageCount($number_of_images)->calculateCredit();
            $chkLmt = Helper::checkImageDailyLimit();
            if ($chkLmt->getStatusCode() === 429) {
                return $chkLmt;
            }
            $driver->redirectIfNoCreditBalance();
            $apiKey = $this->getOpenAiApiKey($user);
            config(['openai.api_key' => $apiKey]);
            set_time_limit(120);

            for ($i = 0; $i < $number_of_images; $i++) {
                $imageDetails = $this->processImageGeneration($engine, $model, $param);
                $savePath = $this->saveImageOutputToStorage($imageDetails);
                $entry = $this->saveEntryToDatabase($imageDetails, $user, $post, $code, $savePath);
                $entry->img_id = 'img-' . $entry->response . '-' . $entry->id;
                $entries[] = $entry;
            }
            $driver->decreaseCredit();
            Usage::getSingle()->updateImageCounts($driver->calculate());
            Cache::lock($lockKey)->release();

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } finally {
            Cache::lock($lockKey)->forceRelease();
        }

        return response()->json(['status' => 'success', 'images' => $entries, 'image_storage' => $this->settings_two->ai_image_storage]);
    }

    private function getOpenAIApiKey(): string
    {
        return ApiHelper::setOpenAiKey();
    }

    private function getStableApiKey(): string
    {
        $stableDiffusionKeys = explode(',', $this->settings_two->stable_diffusion_api_key);

        return $stableDiffusionKeys[array_rand($stableDiffusionKeys)];
    }

    /**
     * Get the default model based on the AI engine.
     *
     * @throws Exception
     */
    private function getDefaultModel(?EngineEnum $engine): ?EntityEnum
    {
        return match ($engine) {
            EngineEnum::OPEN_AI          => $this->getDefaultOpenAiImageModel(),
            EngineEnum::STABLE_DIFFUSION => $this->getStableDiffusionDefaultModel(),
            EngineEnum::FAL_AI           => $this->getDefaultFalAiModel(),

            default                      => throw new Exception(__('Invalid AI Engine')),
        };
    }

    /**
     * Process image generation based on the AI engine.
     *
     * @throws Exception|GuzzleException
     */
    private function processImageGeneration(?EngineEnum $engine, ?EntityEnum $model, array $param): array
    {
        return match ($engine) {
            EngineEnum::OPEN_AI          => $this->processOpenAIImage($model, $param),
            EngineEnum::STABLE_DIFFUSION => $this->processStableDiffusionImage($model, $param),
            EngineEnum::FAL_AI           => $this->processFalAIImage($model, $param),

            default                      => throw new Exception(__('Invalid AI Engine')),
        };
    }

    private function processOpenAIImage(?EntityEnum $model, array $param): array
    {
        $is_demo = Helper::appIsDemo();
        $size = $param['size'];
        $description = $param['description'];
        $style = $param['image_style'] ?? null;
        $lighting = $param['image_lighting'] ?? null;
        $mood = $param['mood'] ?? null;
        $quality = $param['quality'];
        $prompt = $description;
        if (is_null($prompt)) {
            throw new RuntimeException(__('You must provide a prompt'));
        }
        $attributes = [
            'style'    => $style ? "$style style" : null,
            'lighting' => $lighting ? "$lighting lighting" : null,
            'mood'     => $mood ? "$mood mood" : null,
        ];
        $prompt .= ' ' . implode(' ', array_filter($attributes));
        $response = FacadesOpenAI::images()->create([
            'model'           => $model,
            'prompt'          => $prompt,
            'size'            => $is_demo ? $this->getDemoImageSize($model) : $size,
            'response_format' => 'b64_json',
            'quality'         => $is_demo ? 'standard' : $quality,
            'n'               => 1,
        ]);
        $contents = base64_decode($response['data'][0]['b64_json']);
        $nameOfImage = Str::random(12) . '-DALL-E-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png';

        return [
            'prompt'                => $prompt,
            'imageContent'          => $contents,
            'nameOfImage'           => $nameOfImage,
        ];
    }

    /**
     * @throws GuzzleException
     * @throws RandomException
     * @throws JsonException
     */
    private function processStableDiffusionImage(?EntityEnum $model, array $param): array
    {
        $stable_type = $param['type'];
        $prompt = $param['stable_description'];
        if (is_null($prompt)) {
            throw new RuntimeException(__('You must provide a prompt'));
        }

        $negative_prompt = $param['negative_prompt'];
        $style_preset = $param['style_preset'];
        $sampler = $param['sampler'];
        $clip_guidance_preset = $param['clip_guidance_preset'];
        $image_resolution = $param['image_resolution'];
        $init_image = $param['image_src'] ?? null;
        $mood = $param['mood'] ?? null;
        $defaultSdModel = $this->getStableDiffusionDefaultModel()->value;

        $v2BetaModels = [
            EntityEnum::SD_3->value,
            EntityEnum::SD_3_TURBO->value,
            EntityEnum::SD_3_MEDIUM->value,
            EntityEnum::SD_3_LARGE->value,
            EntityEnum::SD_3_LARGE_TURBO->value,
            EntityEnum::CORE->value,
            EntityEnum::ULTRA->value,
        ];
        $width = (int) explode('x', $image_resolution)[0];
        $height = (int) explode('x', $image_resolution)[1];

        if ($defaultSdModel === BedrockEngine::BEDROCK->value && $stable_type === 'text-to-image') {
            $response = $this->bedrockService->invokeStableDiffusion($prompt, random_int(1, 1000000), $width, $height);
            $nameOfImage = Str::random(12) . '-AWS-SD-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png';

            return [
                'prompt'                => $prompt,
                'imageContent'          => $response,
                'nameOfImage'           => $nameOfImage,
            ];
        }
        $stableDiffusionKey = $this->getStableApiKey();
        if (empty($stableDiffusionKey)) {
            throw new RuntimeException(__('You must provide a StableDiffusion API Key.'));
        }

        $sd3Payload = [];
        $baseUri = in_array($defaultSdModel, $v2BetaModels, true) && in_array($stable_type, ['text-to-image', 'image-to-image'], true)
            ? 'https://api.stability.ai/v2beta/stable-image/generate/'
            : 'https://api.stability.ai/v1/generation/';
        $contentType = ($stable_type === 'upscale' || $stable_type === 'image-to-image') ? 'multipart/form-data' : 'application/json';
        $client = new Client([
            'base_uri' => $baseUri,
            'headers'  => [
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $stableDiffusionKey,
                'Accept'        => 'application/json',
            ],
        ]);
        $payload = [
            'cfg_scale'            => 7,
            'clip_guidance_preset' => $clip_guidance_preset ?? 'NONE',
            'samples'              => 1,
            'steps'                => 50,
        ];
        if ($sampler) {
            $payload['sampler'] = $sampler;
        }
        if ($style_preset) {
            $payload['style_preset'] = $style_preset;
        }
        $content_type = 'json';
        switch ($stable_type) {
            case 'multi-prompt':
                $stable_url = 'text-to-image';
                $payload['width'] = $width;
                $payload['height'] = $height;
                $arr = [];
                foreach ($prompt as $p) {
                    $arr[] = [
                        'text'   => $p . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ];
                }
                $prompt = $arr;
                $payload['text_prompts'] = $prompt;

                break;
            case 'upscale':
                $content_type = 'multipart';
                $stable_url = 'image-to-image/upscale';
                $defaultSdModel = 'esrgan-v1-x2plus';
                $payload = [];
                $payload['image'] = $init_image->get();
                $prompt = [
                    [
                        'text'   => $prompt . '-' . Str::random(16),
                        'weight' => 1,
                    ],
                ];

                break;
            case 'image-to-image':
                $content_type = 'multipart';
                $stable_url = $stable_type;
                $payload['init_image'] = $init_image->get();
                $sd3Payload = [
                    [
                        'name'     => 'prompt',
                        'contents' => $prompt,
                    ],
                    [
                        'name'     => 'mode',
                        'contents' => 'image-to-image',
                    ],
                    [
                        'name'     => 'strength',
                        'contents' => 0,
                    ],
                    [
                        'name'     => 'image',
                        'contents' => $init_image->get(),
                        'filename' => $init_image->getClientOriginalName(),
                    ],
                ];
                $prompt = [
                    [
                        'text'   => $prompt . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ],
                ];
                $payload['text_prompts'] = $prompt;

                break;
            default:
                $stable_url = $stable_type;
                $payload['width'] = $width;
                $payload['height'] = $height;
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
                $prompt = [
                    [
                        'text'   => $prompt . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ],
                ];
                $payload['text_prompts'] = $prompt;

                break;
        }
        if ($negative_prompt) {
            $prompt[] = ['text' => $negative_prompt, 'weight' => -1];
        }
        if ($content_type === 'multipart') {
            $multipart = [];
            foreach ($payload as $key => $value) {
                if (! is_array($value)) {
                    $multipart[] = ['name' => $key, 'contents' => $value];

                    continue;
                }
                foreach ($value as $multiKey => $multiValue) {
                    $multiName = $key . '[' . $multiKey . ']' . (is_array($multiValue) ? '[' . key($multiValue) . ']' : '') . '';
                    $multipart[] = ['name' => $multiName, 'contents' => (is_array($multiValue) ? reset($multiValue) : $multiValue)];
                }
            }
            $payload = $multipart;
        }

        try {
            if (in_array($defaultSdModel, $v2BetaModels, true) && in_array($stable_type, ['text-to-image', 'image-to-image'], true)) {
                $defaultSdModel = 'sd3';
                $sd3Payload[] = ['name' => 'model', 'contents' => $defaultSdModel];
                $response = $client->post($defaultSdModel, [
                    'headers'   => ['accept' => 'application/json'],
                    'multipart' => $sd3Payload,
                ]);
            } else {
                $defaultSdModel = $stable_type === 'multi-prompt' ? EntityEnum::STABLE_DIFFUSION_V_1_6->value : $defaultSdModel;
                $response = $client->post("$defaultSdModel/$stable_url", [
                    $content_type => $payload,
                ]);
            }
        } catch (Exception $e) {
            if ($e->hasResponse()) {
                $errorMessage = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($errorMessage, true, 512, JSON_THROW_ON_ERROR);

                throw new RuntimeException($errorData['message']);
            }

            throw new RuntimeException($e->getMessage());
        }

        $body = $response->getBody();
        if ($response->getStatusCode() === 200) {
            $nameOfPrompt = explode(' ', mb_substr($prompt[0]['text'], 0, 15))[0];
            $nameOfImage = Str::random(12) . '-STABLE-' . $nameOfPrompt . '.png';
            if (
                ($stable_type === 'text-to-image' || $stable_type === 'image-to-image') && in_array($defaultSdModel, $v2BetaModels, true)
            ) {
                $contents = base64_decode(json_decode($body, false, 512, JSON_THROW_ON_ERROR)->image);
            } else {
                $contents = base64_decode(json_decode($body, false, 512, JSON_THROW_ON_ERROR)->artifacts[0]->base64);
            }
        } else {
            if ($body->status === 'error') {
                $message = $body->message;
            } else {
                $message = __('Failed, Try Again');
            }

            throw new RuntimeException($message);
        }

        return [
            'prompt'                => $prompt[0]['text'],
            'imageContent'          => $contents,
            'nameOfImage'           => $nameOfImage,
        ];
    }

    private function processFalAIImage(?EntityEnum $model, array $param): array
    {
        $prompt = $param['description_flux_pro'];
        $requestId = FalAIService::generate($prompt, $model);

        return [
            'engine'                => EngineEnum::FAL_AI,
            'requestId'             => $requestId,
            'status'                => 'IN_QUEUE',
            'output'                => asset(self::LOADING_GIF),
            'prompt'                => $prompt,
            'imageContent'          => null,
            'nameOfImage'           => Str::random(12) . '-FLUX-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png',
        ];
    }

    private function saveEntryToDatabase(array $imageDetails, User $user, OpenAIGenerator $post, string $code, string $savePath): UserOpenai
    {
        $data = [
            'team_id'   => $user->team_id,
            'title'     => $imageDetails['nameOfImage'],
            'slug'      => Str::random(7) . Str::slug($user?->fullName()) . '-workbook',
            'user_id'   => $user->id,
            'openai_id' => $post->id,
            'input'     => $imageDetails['prompt'],
            'response'  => $code,
            'output'    => ThumbImage(url($savePath)),
            'hash'      => Str::random(256),
            'credits'   => 1,
            'words'     => 0,
            'storage'   => $this->settings_two->ai_image_storage,
            'payload'   => request()?->all(),
        ];
        if (isset($imageDetails['engine']) && $imageDetails['engine'] === EngineEnum::FAL_AI) {
            $data['request_id'] = $imageDetails['requestId'];
            $data['status'] = $imageDetails['status'];
            $data['output'] = $imageDetails['output'];
        }

        return UserOpenai::create($data);
    }

    private function saveImageOutputToStorage(array $imageDetails): string
    {
        if (isset($imageDetails['engine']) && $imageDetails['engine'] === EngineEnum::FAL_AI) {
            return '/';
        }

        $image_storage = $this->settings_two->ai_image_storage;

        return match ($image_storage) {
            self::STORAGE_S3    => $this->saveImageToS3($imageDetails),
            self::CLOUDFLARE_R2 => $this->saveImageToR2($imageDetails),
            default             => $this->saveImageToLocal($imageDetails),
        };
    }

    private function saveImageToS3(array $imageDetails): string
    {
        $localPath = $this->saveImageToLocal($imageDetails);

        try {
            $uploadedFile = new File($localPath);
            $aws_path = Storage::disk('s3')->put('', $uploadedFile);
            unlink($localPath);
            $fullAWSPath = Storage::disk('s3')->url($aws_path);
        } catch (Exception $e) {
            throw new RuntimeException('AWS Error - ' . $e->getMessage());
        }

        return $fullAWSPath;
    }

    private function saveImageToLocal(array $imageDetails): string
    {
        Storage::disk('public')->put($imageDetails['nameOfImage'], $imageDetails['imageContent']);

        return 'uploads/' . $imageDetails['nameOfImage'];
    }

    private function saveImageToR2(array $imageDetails): string
    {
        Storage::disk('r2')->put($imageDetails['nameOfImage'], $imageDetails['imageContent']);

        return Storage::disk('r2')->url($imageDetails['nameOfImage']);
    }

    private function getEngineCode(?EngineEnum $engine): string
    {
        return match ($engine) {
            EngineEnum::STABLE_DIFFUSION => 'SD',
            EngineEnum::FAL_AI           => 'FL',
            default                      => 'DE',
        };
    }

    private function getDemoImageSize(?EntityEnum $model): string
    {
        return match ($model) {
            EntityEnum::DALL_E_3 => '1024x1024',
            EntityEnum::DALL_E_2 => '256x256',
            default              => '512x512',
        };
    }

    private function getDefaultOpenAiImageModel(): EntityEnum
    {
        $default = match ($this->settings_two->dalle) {
            'dalle3' => EntityEnum::DALL_E_3->slug(),
            'dalle2' => EntityEnum::DALL_E_2->slug(),
            default  => $this->settings_two->dalle,
        };

        return EntityEnum::fromSlug($default) ?? EntityEnum::DALL_E_2;
    }

    private function getStableDiffusionDefaultModel(): EntityEnum
    {
        return EntityEnum::fromSlug($this->settings_two?->stablediffusion_default_model) ?? EntityEnum::SD_3;
    }

    private function getDefaultFalAiModel(): EntityEnum
    {
        return EntityEnum::fromSlug(setting('fal_ai_default_model', EntityEnum::FLUX_PRO->value)) ?? EntityEnum::FLUX_PRO;
    }

    /**
     * Get latest 10 images
     *
     * @OA\Get(
     *      path="/api/aiimage/recent-images",
     *      operationId="getRecentImages",
     *      tags={"AI Image Generation"},
     *      security={{ "passport": {} }},
     *      summary="Get latest 10 images",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function getRecentImages(Request $request)
    {

        $user = $request->user();
        $openai = OpenAIGenerator::where('slug', 'ai_image_generator')->firstOrFail();
        $documents = UserOpenai::where('user_id', $user->id)->where('openai_id', $openai->id)->latest('created_at')->take(10)->get();

        return response()->json($documents, 200);

    }
}
