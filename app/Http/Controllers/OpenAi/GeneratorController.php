<?php

namespace App\Http\Controllers\OpenAi;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity as EntityFacade;
use App\Domains\Entity\Models\Entity;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\PlanHelper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorFilter;
use App\Models\PdfData;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\UserOpenai;
use App\Models\UserOpenaiChat;
use App\Models\UserOpenaiChatMessage;
use App\Services\Stream\StreamService;
use App\Services\VectorService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;
use JsonException;
use Random\RandomException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class GeneratorController extends Controller
{
    protected $settings;

    protected $settings_two;

    public StreamService $streamService;

    public function __construct()
    {
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
        $this->middleware(function (Request $request, $next) {
            ApiHelper::setOpenAiKey($this->settings);

            return $next($request);
        });
        $this->streamService = new StreamService($this->settings, $this->settings_two);
    }

    public function realtime(): View
    {
        return view('panel.admin.openai.realtime.index');
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function buildStreamedOutput(Request $request): ?StreamedResponse
    {
        $template_type = $request->get('template_type', 'chatbot');

        // If the template type is chat, then we will build a chat streamed output or other ai template streamed output
        return match ($template_type) {
            'chatbot', 'vision' => $this->buildChatStreamedOutput($request),
            default => $this->buildOtherStreamedOutput($request),
        };
    }

    // chat template and etc.

    /**
     * @throws JsonException
     * @throws GuzzleException
     */
    public function buildChatStreamedOutput(Request $request): ?StreamedResponse
    {
        $prompt = $request->get('prompt');
        $realtime = $request->get('realtime', false);
        $chat_brand_voice = $request->get('chat_brand_voice');
        $brand_voice_prod = $request->get('brand_voice_prod');
        $chat_id = $request->get('chat_id');
        $chat_type = $request->get('template_type');
        $images = $request->get('images', []);
        $pdfname = $request->get('pdfname', null);
        $pdfpath = $request->get('pdfpath', null);
        $assistant = $request->get('assistant', null);
        $openRouter = null;

        $chatbot_front_model = $request->get('chatbot_front_model', null);

        if (! empty($chatbot_front_model) && (int) setting('open_router_status') === 1 && EntityEnum::fromSlug($chatbot_front_model)->engine() === EngineEnum::OPEN_ROUTER) {
            $openRouter = $chatbot_front_model;
        }

        $user = Auth::user();

        $default_ai_engine = setting('default_ai_engine', EngineEnum::OPEN_AI->value);

        if ($default_ai_engine === EngineEnum::OPEN_AI->value) {
            $chat_bot = $this->settings?->openai_default_model ?: EntityEnum::GPT_4_O->value;
        } elseif ($default_ai_engine === EngineEnum::GEMINI->value) {
            $chat_bot = setting('gemini_default_model', 'gemini-1.5-pro-latest');
        } elseif ($default_ai_engine === EngineEnum::ANTHROPIC->value) {
            $chat_bot = setting('anthropic_default_model', EntityEnum::CLAUDE_3_OPUS->value);
        } elseif ($default_ai_engine === EngineEnum::DEEP_SEEK->value) {
            $chat_bot = setting('deepseek_default_model', EntityEnum::DEEPSEEK_CHAT->value);
        } elseif ($default_ai_engine === EngineEnum::X_AI->value) {
            $chat_bot = setting('xai_default_model', EntityEnum::GROK_2_1212->value);
        } else {
            $chat_bot = $this->settings?->openai_default_model ?: EntityEnum::GPT_4_O->value;
        }

        $chat_bot_model = PlanHelper::userPlanAiModel();
        if ($chat_bot_model && empty($chatbot_front_model)) {
            $default_ai_engine_new = Entity::query()
                ->where('key', $chat_bot)
                ->first()
                ?->getAttribute('default_ai_engine');
            if ($default_ai_engine_new) {
                $chat_bot = $chat_bot_model;
                $default_ai_engine = $default_ai_engine_new;
            }
        }

        if (! empty($chatbot_front_model)) {
            $oldChatbot = $chat_bot;

            $chat_bot = $chatbot_front_model;

            $engine = Entity::query()
                ->where('key', $chat_bot)
                ->first();

            if ($engine) {
                $default_ai_engine = $engine->engine;

                if ($default_ai_engine instanceof EngineEnum) {
                    $default_ai_engine = $default_ai_engine->value;
                } else {
                    $chat_bot = $oldChatbot;
                }
            } else {
                $chat_bot = $oldChatbot;
            }
        }

        $systemRole = EntityEnum::fromSlug($chat_bot)->isBetaEntity() ? 'system' : 'user';

        $history = [];
        $realtimePrompt = $prompt;
        $chat = UserOpenaiChat::with('category')->findOrFail($chat_id);

        // if prompt prefix exists, add it to the prompt
        // if ($chat->category->prompt_prefix != null && !str_starts_with($chat->category->slug, 'ai_')) {
        //     $prompt = "You will now play a character and respond as that character (You will never break character). Your name is". $chat->category->human_name. ". I want you to act as a". $chat->category->role . ". ". $chat->category->prompt_prefix . ' ' . $prompt;
        // }

        // create the message here with default to fill it after stream
        $message = UserOpenaiChatMessage::create([
            'user_id'             => $user->id,
            'user_openai_chat_id' => $chat->id,
            'input'               => $prompt,
            'response'            => null,
            'realtime'            => $realtime ?? 0,
            'output'              => __("(If you encounter this message, please attempt to send your message again. If the error persists beyond multiple attempts, please don't hesitate to contact us for assistance!)"),
            'hash'                => Str::random(256),
            'credits'             => 0,
            'words'               => 0,
            'images'              => $images,
            'pdfName'             => $pdfname,
            'pdfPath'             => $pdfpath,
        ]);

        // check if their completions for the template
        $category = $chat->category;
        if ($category->chat_completions) {
            $chat_completions = json_decode($category->chat_completions, true, 512, JSON_THROW_ON_ERROR);
            foreach ($chat_completions as $item) {
                $history[] = [
                    'role'    => $item['role'],
                    'content' => $item['content'] ?? '',
                ];
            }
        } else {
            $history[] = ['role' => $systemRole, 'content' => 'You are a helpful assistant.'];
        }

        $isFileSearch = setting('openai_file_search', 0) && $chat->openai_vector_id !== null;

        // if file attached, get the content of the file
        if (! $isFileSearch && ($category->chatbot_id || PdfData::where('chat_id', $chat_id)->exists())) {
            try {
                $extra_prompt = (new VectorService)->getMostSimilarText($prompt, $chat_id, 2, $category->chatbot_id);
                if ($extra_prompt) {
                    if ($chat->category->slug === 'ai_webchat') {
                        $history[] = [
                            'role'    => $systemRole,
                            'content' => "You are a Web Page Analyzer assistant. When referring to content from a specific website or link, please include a brief summary or context of the content. If users inquire about the content or purpose of the website/link, provide assistance professionally without explicitly mentioning the content. Website/link content: \n$extra_prompt",
                        ];
                    } else {
                        $history[] = [
                            'role'    => $systemRole,
                            'content' => "You are a File Analyzer assistant. When referring to content from a specific file, please include a brief summary or context of the content. If users inquire about the content or purpose of the file, provide assistance professionally without explicitly mentioning the content. File content: \n$extra_prompt",
                        ];
                    }
                }
            } catch (Throwable $th) {
            }
        } elseif ($category && $category?->instructions) {
            $history[] = ['role' => $systemRole, 'content' => $category->instructions];
        }
        // follow the context of the last 3 messages
        $lastThreeMessageQuery = $chat->messages()
            ->whereNotNull('input')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->reverse();

        // if one of the last 3 messages contain images, then write proper history for vision model for all messages
        $contain_images = $this->checkIfHistoryContainsImages($lastThreeMessageQuery);

        // if the last 3 messages are not empty, add them to the history
        $count = count($lastThreeMessageQuery);
        if ($count > 1) {
            foreach ($lastThreeMessageQuery as $threeMessage) {
                if ($contain_images) {
                    $history[] = [
                        'role'    => 'user',
                        'content' => array_merge(
                            [
                                [
                                    'type' => 'text',
                                    'text' => $threeMessage->input,
                                ],
                            ],
                            collect($threeMessage->images)->map(static function ($item) use ($assistant) {
                                $images = explode(',', $item);
                                $imageResults = [];
                                if (! empty($images)) {
                                    foreach ($images as $image) {
                                        if (Str::startsWith($image, 'http')) {
                                            $imageData = file_get_contents($image);
                                        } else {
                                            $imageData = file_get_contents(ltrim($image, '/'));
                                        }
                                        $base64Image = base64_encode($imageData);

                                        if ($assistant !== null) {
                                            $imageResults[] = [
                                                'type'      => 'image_url',
                                                'image_url' => [
                                                    'url' => $image,
                                                ],
                                            ];
                                        } else {
                                            $imageResults[] = [
                                                'type'      => 'image_url',
                                                'image_url' => [
                                                    'url' => 'data:image/png;base64,' . $base64Image,
                                                ],
                                            ];
                                        }
                                    }
                                }

                                return $imageResults;
                            })->reject(fn ($value) => empty($value))->flatten(1)->toArray()
                        ),
                    ];
                } else {
                    $history[] = ['role' => 'user', 'content' => $threeMessage->input ?? ''];
                }

                if ($threeMessage->output !== null && $threeMessage->output !== '') {
                    $history[] = ['role' => 'assistant', 'content' => $threeMessage->output];
                }
            }
        }

        // check if the chat brand voice is set
        $history = $this->checkBrandVoice($chat_brand_voice, $brand_voice_prod, $history);
        if (empty($images) && $chat->category->slug !== 'ai_vision') {
            if ($realtime) {
                if (setting('default_realtime', 'serper') == 'serper' &&
                    ! is_null($this->settings_two->serper_api_key)) {
                    $final_prompt = $this->getRealtimePrompt($realtimePrompt);

                } elseif (setting('default_realtime') == 'perplexity' &&
                ! is_null(setting('perplexity_key'))) {
                    $final_prompt = $this->realtimePromptPerplexity($realtimePrompt);
                }
                $history[] = ['role' => 'user', 'content' => $final_prompt ?? ''];
            } else {
                $history[] = ['role' => 'user', 'content' => $prompt ?? ''];
            }
        } else { // in this case we need to use vision model and its not included in OpenAI lib yet..
            if ($chat_type === 'vision') {
                $history[] = [
                    'role'    => $systemRole,
                    'content' => 'You will now play a character and respond as that character (You will never break character). Your name is Vision AI. Must not introduce by yourself as well as greetings. Help also with asked questions based on previous responses and images if exists.',
                ];
            }
            $images = explode(',', $request->images);
            $history[] = [
                'role'    => 'user',
                'content' => array_merge(
                    [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                    ],
                    collect($images)->map(static function ($item) use ($assistant) {
                        if (! empty($item)) {
                            if (Str::startsWith($item, 'http')) {
                                $imageData = file_get_contents($item);
                            } else {
                                $imageData = file_get_contents(substr($item, 1, strlen($item) - 1));
                            }
                            $base64Image = base64_encode($imageData);
                            if ($assistant !== null) {
                                return [
                                    'type'      => 'image_url',
                                    'image_url' => [
                                        'url' => $item,
                                    ],
                                ];
                            }

                            return [
                                'type'      => 'image_url',
                                'image_url' => [
                                    'url' => 'data:image/png;base64,' . $base64Image,
                                ],
                            ];
                        }

                        return null;
                    })->reject(null)->toArray() // Filter out NULL values
                ),
            ];
            $contain_images = true;
        }

        return $this->streamService->ChatStream(
            $chat_bot,
            $history,
            $message,
            $chat_type,
            $contain_images,
            $default_ai_engine,
            assistant: $assistant,
            openRouter: $openRouter,
            responsesApi: $isFileSearch
        );
    }

    private function checkBrandVoice($chat_brand_voice, $brand_voice_prod, $history)
    {
        if (! empty($chat_brand_voice) && ! empty($brand_voice_prod)) {
            // check if there is a company input included in the request
            $company = Company::find($chat_brand_voice);
            $product = Product::find($brand_voice_prod);
            if ($company && $product) {
                $type = $product->type === 0 ? 'Service' : 'Product';
                $prompt = "Focus on my company and {$type}'s information: \n";
                // Company information
                if ($company->name) {
                    $prompt .= "The company's name is {$company->name}. ";
                }
                // explode industry
                $industry = explode(',', $company->industry);
                $count = count($industry);
                if ($count > 0) {
                    $prompt .= 'The company is in the ';
                    foreach ($industry as $index => $ind) {
                        $prompt .= $ind;
                        if ($index < $count - 1) {
                            $prompt .= ' and ';
                        }
                    }
                }
                if ($company->website) {
                    $prompt .= ". The company's website is {$company->website}. ";
                }
                if ($company->target_audience) {
                    $prompt .= "The company's target audience is: {$company->target_audience}. ";
                }
                if ($company->tagline) {
                    $prompt .= "The company's tagline is {$company->tagline}. ";
                }
                if ($company->description) {
                    $prompt .= "The company's description is {$company->description}. ";
                }
                if ($product) {
                    if ($product->key_features) {
                        $prompt .= "The {$product->type}'s key features are {$product->key_features}. ";
                    }

                    if ($product->name) {
                        $prompt .= "The {$product->type}'s name is {$product->name}. \n";
                    }
                }
                $prompt .= "\n";
                $history[] = ['role' => 'user', 'content' => $prompt];

                return $history;
            }
        }

        return $history;
    }

    // ai writer template and etc.
    public function buildOtherStreamedOutput(Request $request): StreamedResponse
    {
        $default_ai_engine = setting('default_ai_engine', EngineEnum::OPEN_AI->value);

        if ($default_ai_engine === EngineEnum::OPEN_AI->value) {
            $chatBot = ! $this->settings?->openai_default_model ? EntityEnum::GPT_3_5_TURBO->value : $this->settings?->openai_default_model;
        } elseif ($default_ai_engine === EngineEnum::GEMINI->value) {
            $chatBot = setting('gemini_default_model', EntityEnum::GEMINI_1_5_PRO_LATEST->value);
        } elseif ($default_ai_engine === EngineEnum::ANTHROPIC->value) {
            $chatBot = setting('anthropic_default_model', EntityEnum::CLAUDE_3_OPUS->value);
        } elseif ($default_ai_engine === EngineEnum::DEEP_SEEK->value) {
            $chatBot = setting('deepseek_default_model', EntityEnum::DEEPSEEK_CHAT->value);
        } elseif ($default_ai_engine === EngineEnum::X_AI->value) {
            $chatBot = setting('xai_default_model', EntityEnum::GROK_2_1212->value);
        } else {
            $chatBot = ! $this->settings?->openai_default_model ? EntityEnum::GPT_3_5_TURBO->value : $this->settings?->openai_default_model;
        }

        if ($chat_bot_model = PlanHelper::userPlanAiModel()) {

            $default_ai_engine_new = Entity::query()
                ->where('key', $chatBot)
                ->first()
                ?->getAttribute('default_ai_engine');

            if ($default_ai_engine_new) {
                $chatBot = $chat_bot_model;
                $default_ai_engine = $default_ai_engine_new;
            }
        }

        $chatbot_front_model = $request->get('chatbot_front_model', null);

        if (! empty($chatbot_front_model)) {
            $oldChatbot = $chatBot;

            $engine = Entity::query()
                ->where('key', $chatbot_front_model)
                ->first();

            if ($engine) {
                $default_ai_engine = $engine->engine;
                $chatBot = $chatbot_front_model;
                if ($default_ai_engine instanceof EngineEnum) {
                    $default_ai_engine = $default_ai_engine->value;
                } else {
                    $chatBot = $oldChatbot;
                }
            }
        }

        return $this->streamService->OtherStream($request, $chatBot, $default_ai_engine);
    }

    // reduce tokens when the stream is interrupted
    public function reduceTokensWhenIntterruptStream(Request $request, $type): void
    {
        $this->streamService->reduceTokensWhenIntterruptStream($request, $type);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    private function getRealtimePrompt($realtimePrompt): ?string
    {
        $driver = EntityFacade::driver(EntityEnum::SERPER);

        if (! $driver->hasCreditBalance()) {
            echo PHP_EOL;
            echo "event: data\n";
            echo 'data: ' . __('You have no realtime search credits left. Please buy more credits to continue.');
            echo "\n\n";
            flush();
            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();

            return null;
        }

        $client = new Client;
        $headers = [
            'X-API-KEY'    => $this->settings_two->serper_api_key,
            'Content-Type' => 'application/json',
        ];
        $body = [
            'q' => $realtimePrompt,
        ];
        $response = $client->post('https://google.serper.dev/search', [
            'headers' => $headers,
            'json'    => $body,
        ]);
        $toGPT = $response->getBody()->getContents();

        try {
            $toGPT = json_decode($toGPT, false, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $th) {
        }

        $driver->input($realtimePrompt)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        return 'Prompt: ' . $realtimePrompt .
            '\n\nWeb search json results: '
            . json_encode($toGPT, JSON_THROW_ON_ERROR) .
            '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';
    }

    public function realtimePromptPerplexity($realtimePrompt)
    {

        $url = 'https://api.perplexity.ai/chat/completions';
        $token = setting('perplexity_key');

        $payload = [
            'model'    => 'llama-3.1-sonar-small-128k-online',
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => $realtimePrompt,
                ],
            ],
        ];

        try {
            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $response = $data['choices'][0]['message']['content'];

                return 'Prompt: ' . $realtimePrompt .
                    '\n\nWeb search results: '
                    . $response .
                    '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';

            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => $response->body(),
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function checkIfHistoryContainsImages($lastThreeMessages): bool
    {
        if (! is_iterable($lastThreeMessages)) {
            return false;
        }

        return collect($lastThreeMessages)->contains(static function ($message) {
            return ! empty($message->images);
        });
    }

    /**
     * @throws RandomException
     */
    public function index($workbook_slug = null)
    {
        abort_if(Helper::setting('feature_ai_advanced_editor') === 0, 404);
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
        $settings_two = SettingTwo::getCache();
        if ($settings_two->openai_default_stream_server === 'backend') {
            $apikeyPart1 = base64_encode(random_int(1, 100));
            $apikeyPart2 = base64_encode(random_int(1, 100));
            $apikeyPart3 = base64_encode(random_int(1, 100));
        } else {
            $apiKey = ApiHelper::setOpenAiKey();
            $len = strlen($apiKey);
            $len = max($len, 6);
            $parts[] = substr($apiKey, 0, $l[] = random_int(1, $len - 5));
            $parts[] = substr($apiKey, $l[0], $l[] = random_int(1, $len - $l[0] - 3));
            $parts[] = substr($apiKey, array_sum($l));
            $apikeyPart1 = base64_encode($parts[0]);
            $apikeyPart2 = base64_encode($parts[1]);
            $apikeyPart3 = base64_encode($parts[2]);
        }
        if ($workbook_slug) {
            $workbook = UserOpenai::where('slug', $workbook_slug)->where('user_id', auth()->user()->id)->first();
        } else {
            $workbook = null;
        }

        return view('panel.user.generator.index', [
            'list' => OpenAIGenerator::query()
                ->where('active', true)
                ->get(),
            'filters' => OpenaiGeneratorFilter::query()
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereNull('user_id');
                })
                ->get(),
            'apikeyPart1' => $apikeyPart1,
            'apikeyPart2' => $apikeyPart2,
            'apikeyPart3' => $apikeyPart3,
            'apiUrl'      => $apiUrl,
            'workbook'    => $workbook,
            'models'      => Entity::planModels(),
        ]);
    }

    public function generator(Request $request, $slug): void {}

    public function generatorOptions(Request $request, $slug): string
    {
        $openai = OpenAIGenerator::query()
            ->where('slug', $slug)
            ->firstOrFail();
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
        $settings_two = SettingTwo::getCache();
        if ($settings_two->openai_default_stream_server === 'backend') {
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

        $apiSearch = base64_encode('https://google.serper.dev/search');
        $auth = $request->user();

        $models = Entity::planModels();

        return view(
            'panel.user.generator.components.generator-options',
            compact(
                'slug',
                'openai',
                'apiSearch',
                'apikeyPart1',
                'apikeyPart2',
                'apikeyPart3',
                'apiUrl',
                'auth',
                'models'
            )
        )->render();
    }

    protected function openai(Request $request): Builder
    {
        $team = $request->user()->getAttribute('team');

        $myCreatedTeam = $request->user()->getAttribute('myCreatedTeam');

        return UserOpenai::query()
            ->where(function (Builder $query) use ($team, $myCreatedTeam) {
                $query->where('user_id', auth()->id())
                    ->when($team || $myCreatedTeam, function ($query) use ($team, $myCreatedTeam) {
                        if ($team && $team?->is_shared) {
                            $query->orWhere('team_id', $team->id);
                        }
                        if ($myCreatedTeam) {
                            $query->orWhere('team_id', $myCreatedTeam->id);
                        }
                    });
            });
    }
}
