<?php

namespace App\Extensions\AIWebChat\System\Http\Controllers;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Helpers\Classes\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\OpenaiGeneratorChatCategory;
use App\Models\PdfData;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use App\Models\UserOpenaiChat;
use App\Models\UserOpenaiChatMessage;
use App\Services\Chatbot\LinkCrawler;
use App\Services\GatewaySelector;
use App\Services\VectorService;
use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;

class AIWebChatController extends Controller
{
    public function __construct()
    {
        ApiHelper::setOpenAiKey();

        set_time_limit(120);
    }

    private function getOpenAiApiKey(?User $user): string
    {
        return ApiHelper::setOpenAiKey();
    }

    public function openAIGeneratorWorkbook()
    {
        $setting = Setting::getCache();
        $setting2 = SettingTwo::getCache();
        $apiKey = ApiHelper::setOpenAiKey($setting);
        $len = strlen($apiKey);
        $len = max($len, 6);
        $parts[] = substr($apiKey, 0, $l[] = rand(1, $len - 5));
        $parts[] = substr($apiKey, $l[0], $l[] = rand(1, $len - $l[0] - 3));
        $parts[] = substr($apiKey, array_sum($l));
        $apikeyPart1 = base64_encode($parts[0]);
        $apikeyPart2 = base64_encode($parts[1]);
        $apikeyPart3 = base64_encode($parts[2]);
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');

        $apiSearch = base64_encode('https://google.serper.dev/search');
        $apiSearchId = base64_encode($setting2->serper_api_key);

        $userId = Auth::id();

        $activeSub = getCurrentActiveSubscription($userId);

        if ($activeSub != null) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSubY != null) {
                $gateway = $activeSubY->paid_with;
            }
        }

        try {
            $isPaid = GatewaySelector::selectGateway($gateway)::getSubscriptionStatus();
        } catch (Exception $e) {
            $isPaid = false;
        }

        $category = OpenaiGeneratorChatCategory::query()->whereSlug('ai_webchat')->firstOrFail();

        if ($isPaid == false && $category->plan == 'premium' && auth()->user()->type !== 'admin') {
            return redirect()->back()->with(['message' => __('Needs a Premium access'), 'type' => 'error']);
        }

        $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $category->id)->orderBy('updated_at', 'desc');
        $list = $list->get();
        $chat = $list->first();
        $aiList = OpenaiGeneratorChatCategory::all();
        $streamUrl = route('dashboard.user.openai.webchat.stream');
        $lastThreeMessage = null;
        $chat_completions = null;
        if ($chat != null) {
            $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc')->take(2);
            $lastThreeMessage = $lastThreeMessageQuery->get()->reverse();
            $category = OpenaiGeneratorChatCategory::where('id', $chat->openai_chat_category_id)->first();
            $chat_completions = str_replace(["\r", "\n"], '', $category->chat_completions) ?? null;

            if ($chat_completions != null) {
                $chat_completions = json_decode($chat_completions, true);
            }
        }
        $generators = OpenaiGeneratorChatCategory::query()
            ->whereNotIn('slug', [
                'ai_vision', 'ai_webchat', 'ai_pdf',
            ])
            ->when(Auth::user()->isUser(), function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('user_id')->orWhere('user_id', Auth::id());
                });
            })
            ->get();

        return view('web-chat::webchat', compact(
            'category',
            'apiSearch',
            'generators',
            'apiSearchId',
            'list',
            'chat',
            'aiList',
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'apiUrl',
            'lastThreeMessage',
            'chat_completions',
            'streamUrl'
        ));
    }

    public function openChatAreaContainer(Request $request): JsonResponse
    {
        /** @var UserOpenaiChat $chat */
        $chat = UserOpenaiChat::query()
            ->findOrFail($request->get('chat_id'));

        if (setting('realtime_voice_chat', 0)) {
            $apiKey = $this->getOpenAiApiKey(Auth::user());
            $len = strlen($apiKey);
            $len = max($len, 6);
            $parts[] = substr($apiKey, 0, $l[] = random_int(1, $len - 5));
            $parts[] = substr($apiKey, $l[0], $l[] = random_int(1, $len - $l[0] - 3));
            $parts[] = substr($apiKey, array_sum($l));
            $apikeyPart1 = base64_encode($parts[0]);
            $apikeyPart2 = base64_encode($parts[1]);
            $apikeyPart3 = base64_encode($parts[2]);
        } else {
            $apikeyPart1 = base64_encode(random_int(1, 100));
            $apikeyPart2 = base64_encode(random_int(1, 100));
            $apikeyPart3 = base64_encode(random_int(1, 100));
        }

        $html = view('web-chat::components.webchat_area_container', [
            'chat'        => $chat,
            'category'    => $chat->getAttribute('category'),
            'apikeyPart1' => $apikeyPart1,
            'apikeyPart2' => $apikeyPart2,
            'apikeyPart3' => $apikeyPart3,
        ])->render();

        $lastThreeMessageQuery = $chat
            ->messages()
            ->whereNotNull('input')
            ->orderBy('created_at', 'desc')
            ->take(2)->get()->toArray();

        return response()->json([
            'html'             => $html,
            'lastThreeMessage' => $lastThreeMessageQuery,
        ]);
    }

    public function startNewChat(Request $request)
    {
        ApiHelper::setOpenAiKey();
        set_time_limit(240);

        $url = $request->website_url;
        $page = null;
        $title = ' AI Web Chat ';
        if ($url) {

            $crawler = new LinkCrawler($url);

            $crawler->crawl(true);

            $content = $crawler->getContents();

            $html = \Illuminate\Support\Arr::last($content);

            $client = new Client;

            $response = $client->request('GET', $url);

            $htmlForTitle = $response->getBody()->getContents();

            $dom = new DOMDocument('1.0', 'UTF-8');
            $internalErrors = libxml_use_internal_errors(true);
            $dom->loadHTML($htmlForTitle);

            $title = $dom->getElementsByTagName('title')->item(0)->textContent;

            $page = $html;
            $text = $html;

            libxml_use_internal_errors($internalErrors);
        }

        $page = str_replace("\n", '', $page);

        $category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->firstOrFail();
        $chat = new UserOpenaiChat;
        $chat->user_id = Auth::id();
        $chat->team_id = Auth::user()->team_id;
        $chat->openai_chat_category_id = $category->id;
        $chat->title = $title;
        $chat->website_url = $url;
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->save();

        $countwords = strlen($page) / 3500 + 1;
        error_log($countwords);
        for ($i = 0; $i < $countwords; $i++) {
            if (3500 * $i + 4000 > strlen($page)) {
                try {
                    $subtxt = substr($page, 3500 * $i, strlen($page) - 3500 * $i);
                    $subtxt = mb_convert_encoding($subtxt, 'UTF-8', 'UTF-8');
                    $subtxt = iconv('UTF-8', 'UTF-8//IGNORE', $subtxt);
                    $response = FacadesOpenAI::embeddings()->create([
                        'model' => 'text-embedding-ada-002',
                        'input' => $subtxt,
                    ]);

                    if (strlen(substr($page, 3500 * $i, strlen($page) - 3500 * $i)) > 10) {

                        $chatpdf = new PdfData;

                        $chatpdf->chat_id = $chat->id;
                        $chatpdf->content = substr($page, 3500 * $i, strlen($page) - 3500 * $i);
                        $chatpdf->vector = json_encode($response->embeddings[0]->embedding);

                        $chatpdf->save();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                }
            } else {
                try {
                    $subtxt = substr($page, 3500 * $i, 4000);
                    $subtxt = mb_convert_encoding($subtxt, 'UTF-8', 'UTF-8');
                    $subtxt = iconv('UTF-8', 'UTF-8//IGNORE', $subtxt);
                    $response = FacadesOpenAI::embeddings()->create([
                        'model' => 'text-embedding-ada-002',
                        'input' => $subtxt,
                    ]);
                    if (strlen(substr($page, 3500 * $i, 4000)) > 10) {
                        $chatpdf = new PdfData;

                        $chatpdf->chat_id = $chat->id;
                        $chatpdf->content = substr($page, 3500 * $i, 4000);
                        $chatpdf->vector = json_encode($response->embeddings[0]->embedding);

                        $chatpdf->save();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                }
            }
        }

        $message = new UserOpenaiChatMessage;
        $message->user_openai_chat_id = $chat->id;
        $message->user_id = Auth::id();
        $message->response = 'First Initiation';
        if ($category->slug != 'ai_vision' || $category->slug != 'ai_pdf') {
            if ($category->role == 'default') {
                $output = __('Hi! I am') . ' ' . $category->name . __(', and I\'m here to answer all your questions');
            } else {
                $output = __('Hi! I am') . ' ' . $category->human_name . __(', and I\'m') . ' ' . $category->role . '. ' . $category->helps_with;
            }
        } else {
            $output = null;
        }
        $message->output = $output;
        $message->hash = Str::random(256);
        $message->credits = 0;
        $message->words = 0;
        $message->save();

        if (setting('realtime_voice_chat', 0)) {
            $apiKey = $this->getOpenAiApiKey(Auth::user());
            $len = strlen($apiKey);
            $len = max($len, 6);
            $parts[] = substr($apiKey, 0, $l[] = random_int(1, $len - 5));
            $parts[] = substr($apiKey, $l[0], $l[] = random_int(1, $len - $l[0] - 3));
            $parts[] = substr($apiKey, array_sum($l));
            $apikeyPart1 = base64_encode($parts[0]);
            $apikeyPart2 = base64_encode($parts[1]);
            $apikeyPart3 = base64_encode($parts[2]);
        } else {
            $apikeyPart1 = base64_encode(random_int(1, 100));
            $apikeyPart2 = base64_encode(random_int(1, 100));
            $apikeyPart3 = base64_encode(random_int(1, 100));
        }

        $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $category->id)->where('is_chatbot', 0)->orderBy('updated_at', 'desc')->get();
        $html = view('web-chat::components.webchat_area_container',
            compact(
                'chat',
                'category',
                'apikeyPart1',
                'apikeyPart2',
                'apikeyPart3'
            )
        )->render();
        $html2 = view('panel.user.openai_chat.components.chat_sidebar_list', compact('list', 'chat'))->render();

        return response()->json(compact('html', 'html2', 'chat'));
    }

    public function chatStream(Request $request)
    {
        $openaiKey = ApiHelper::setOpenAiKey();

        $openai_model = Setting::getCache()?->openai_default_model;
        $setting2 = SettingTwo::getCache();

        $client = OpenAI::factory()
            ->withApiKey($openaiKey)
            ->withHttpClient(new \GuzzleHttp\Client)
            ->make();

        session_start();
        header('Content-type: text/event-stream');
        header('Cache-Control: no-cache');
        ob_end_flush();

        $vectorService = new VectorService;

        // Add previous chat history to the prompt
        $prompt = $request->get('message');
        $realtime = $request->get('realtime');
        $categoryId = $request->get('category');
        $realtimePrompt = $prompt;

        try {
            $extra_prompt = $vectorService->getMostSimilarText($prompt, $request->chat_id, 2);
        } catch (Exception $e) {
        }

        $type = $request->get('type');
        // $images = json_decode($request->get('images'), true);
        $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $categoryId)->orderBy('updated_at', 'desc');

        $list = $list->get();
        $chat = $list->first();
        $category = $chat->category;
        $prefix = '';
        if ($category->prompt_prefix != null) {
            $prefix = "$category->prompt_prefix you will now play a character and respond as that character (You will never break character). Your name is $category->human_name but do not introduce by yourself as well as greetings.";
        }

        $messages = [[
            'role'    => 'assistant',
            'content' => $prefix,
        ]];

        $lastThreeMessage = null;
        if ($chat != null) {
            $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc')->take(2);
            $lastThreeMessage = $lastThreeMessageQuery->get()->reverse();
        }

        if ($category->chat_completions == null) {
            $category->chat_completions = '[]';
        }

        // foreach ($lastThreeMessage as $entry) {
        //   if ($entry->output == null) {
        //     $entry->output = "";
        //   }
        //   array_push($messages, array(
        //     "role" => "user",
        //     "content" => $entry->input
        //   ));
        //   array_push($messages, array(
        //     "role" => "assistant",
        //     "content" => $entry->output
        //   ));

        //   $messages = array_merge($messages, json_decode($category->chat_completions, true));
        // }
        if ($extra_prompt == '') {

            if ($realtime == 1) {
                if (setting('default_realtime', 'serper') == 'serper' &&
                    ! is_null($setting2->serper_api_key)) {
                    $sclient = new Client;
                    $headers = [
                        'X-API-KEY'    => $setting2->serper_api_key,
                        'Content-Type' => 'application/json',
                    ];
                    $body = [
                        'q' => $realtimePrompt,
                    ];
                    $response = $sclient->post('https://google.serper.dev/search', [
                        'headers' => $headers,
                        'json'    => $body,
                    ]);
                    $toGPT = $response->getBody()->getContents();

                    try {
                        $toGPT = json_decode($toGPT);
                    } catch (Throwable $th) {
                    }

                    $final_prompt =
                        'Prompt: ' . $realtimePrompt .
                        '\n\nWeb search json results: '
                        . json_encode($toGPT) .
                        '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';

                    array_push($messages, [
                        'role'    => 'user',
                        'content' => $final_prompt,
                    ]);

                } elseif (setting('default_realtime') == 'perplexity' &&
                    ! is_null(setting('perplexity_key'))) {

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
                            $final_prompt = 'Prompt: ' . $realtimePrompt .
                                '\n\nWeb search results: '
                                . $response .
                                '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';

                            array_push($messages, [
                                'role'    => 'user',
                                'content' => $final_prompt,
                            ]);
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
            } else {
                array_push($messages, [
                    'role'    => 'user',
                    'content' => $prompt,
                ]);
            }
        } else {
            array_push($messages, [
                'role'    => 'user',
                'content' => "'Content' means web page content. Must not reference previous chats if user asking about content of web page content. Must reference web page content if only user is asking about content or webpage content. Else just response as an assistant shortly and professionaly without must not referencing web page content. \n\n\n\nUser question: $prompt \n\n\n\n\n Web Page Content: \n $extra_prompt",
            ]);
        }
        // send request to openai

        try {
            if ($type == 'chat') {
                $result = $client->chat()->createStreamed([
                    'model'    => $openai_model,
                    'messages' => $messages,
                ]);
                foreach ($result as $response) {
                    echo "event: data\n";
                    echo 'data: ' . json_encode(['message' => $response->choices[0]->delta->content]) . "\n\n";
                    flush();
                }
            } elseif ($type == 'vision') {
                $images = json_decode($request->get('images'), true);

                $gclient = new Client;
                $openaiApiKey = ApiHelper::setOpenAiKey();

                $url = 'https://api.openai.com/v1/chat/completions';

                $response = $gclient->post(
                    $url,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $openaiApiKey,
                        ],
                        'json' => [
                            'model'    => 'gpt-4-vision-preview',
                            'messages' => [
                                [
                                    'role'    => 'user',
                                    'content' => array_merge(
                                        [
                                            [
                                                'type' => 'text',
                                                'text' => $prompt,
                                            ],
                                        ],
                                        collect($images)->map(function ($item) {
                                            if (Str::startsWith($item, 'http')) {
                                                $imageData = file_get_contents($item);
                                            } else {
                                                $imageData = file_get_contents(substr($item, 1, strlen($item) - 1));
                                            }
                                            $base64Image = base64_encode($imageData);

                                            return [
                                                'type'      => 'image_url',
                                                'image_url' => [
                                                    'url' => 'data:image/png;base64,' . $base64Image,
                                                ],
                                            ];
                                        })->toArray()
                                    ),
                                ],
                            ],
                            'max_tokens' => 3500,
                            'stream'     => true,
                        ],
                    ],
                );

                foreach (explode("\n", $response->getBody()->getContents()) as $chunk) {
                    if (strlen($chunk) > 5 && $chunk != 'data: [DONE]' && isset(json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content)) {
                        echo "event: data\n";
                        echo 'data: ' . json_encode(['message' => json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content]) . "\n\n";
                        flush();
                    }
                }
            }

            echo "event: stop\n";
            echo "data: stopped\n\n";
        } catch (Exception $e) {
        }
    }

    /**
     * @throws Exception
     */
    public function chatOutput(Request $request)
    {
        if ($request->isMethod('get')) {
            $type = $request->type;
            $images = explode(',', $request->images);
            $chat_bot = Setting::getCache()?->openai_default_model ?? 'gpt-3.5-turbo';
            if ($chat_bot === 'gpt-4-vision-preview') {
                $chat_bot = 'gpt-4-1106-preview';
            }
            $chat_id = $request->chat_id;
            $message_id = $request->message_id;

            $driver = Entity::driver(EntityEnum::fromSlug($chat_bot));
            $driver->redirectIfNoCreditBalance();
            $message = UserOpenaiChatMessage::whereId($message_id)->first();
            $prompt = $message->input;
            $extra_prompt = '';

            try {
                $vectorService = new VectorService;
                $extra_prompt = $vectorService->getMostSimilarText($prompt, $chat_id, 2);
            } catch (Exception $e) {
            }

            return response()->stream(function () use ($driver, $request, $chat_id, $message_id, $prompt, $chat_bot, $type, $images, $extra_prompt) {
                if ($type === 'chat') {
                    try {
                        $stream = FacadesOpenAI::chat()->createStreamed([
                            'model'    => $chat_bot,
                            'messages' => [
                                [
                                    'role'    => 'user',
                                    'content' => "'Content' means web page content. Must not reference previous chats if user asking about content of web page content. Must reference web page content if only user is asking about content or webpage content. Else just response as an assistant shortly and professionaly without must not referencing web page content. \n\n\n\nUser question: $prompt \n\n\n\n\n Web Page Content: \n $extra_prompt",
                                ],
                            ],
                            'presence_penalty'  => 0.6,
                            'frequency_penalty' => 0,
                        ]);
                        $total_used_tokens = 0;
                        $output = '';
                        $responsedText = '';

                        foreach ($stream ?? [] as $response) {

                            if (isset($response['choices'][0]['delta']['content'])) {
                                $message = $response['choices'][0]['delta']['content'];
                                $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                                $output .= $messageFix;
                                $responsedText .= $message;
                                $total_used_tokens += countWords($message);
                                $string_length = Str::length($messageFix);
                                $needChars = 6000 - $string_length;
                                $random_text = Str::random($needChars);

                                echo PHP_EOL;
                                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                                ob_flush();
                                flush();
                                usleep(5000);
                            }
                            if (connection_aborted()) {
                                break;
                            }
                        }
                    } catch (Exception $exception) {

                        $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message ' . $exception->getMessage();
                        echo "data: $messageError";
                        echo "\n\n";
                        ob_flush();
                        flush();
                        echo 'data: [DONE]';
                        echo "\n\n";
                        ob_flush();
                        flush();
                        usleep(50000);
                    }
                } elseif ($type === 'vision') {
                    try {
                        $gclient = new Client;
                        $openaiApiKey = ApiHelper::setOpenAiKey();
                        $url = 'https://api.openai.com/v1/chat/completions';
                        $response = $gclient->post(
                            $url,
                            [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $openaiApiKey,
                                ],
                                'json' => [
                                    'model'    => 'gpt-4-vision-preview',
                                    'messages' => [
                                        [
                                            'role'    => 'user',
                                            'content' => array_merge(
                                                [
                                                    [
                                                        'type' => 'text',
                                                        'text' => $request->prompt,
                                                    ],
                                                ],
                                                collect($images)->map(function ($item) {
                                                    if (Str::startsWith($item, 'http')) {
                                                        $imageData = file_get_contents($item);
                                                    } else {
                                                        $imageData = file_get_contents(substr($item, 1, strlen($item) - 1));
                                                    }
                                                    $base64Image = base64_encode($imageData);

                                                    return [
                                                        'type'      => 'image_url',
                                                        'image_url' => [
                                                            'url' => 'data:image/png;base64,' . $base64Image,
                                                        ],
                                                    ];
                                                })->toArray()
                                            ),
                                        ],
                                    ],
                                    'max_tokens' => 3500,
                                    'stream'     => true,
                                ],
                            ],
                        );
                    } catch (Exception $exception) {
                        $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message ' . $exception->getMessage();
                        echo "data: $messageError";
                        echo "\n\n";
                        ob_flush();
                        flush();
                        echo 'data: [DONE]';
                        echo "\n\n";
                        ob_flush();
                        flush();
                        usleep(50000);
                    }
                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';
                    foreach (explode("\n", $response->getBody()->getContents()) as $chunk) {
                        if (strlen($chunk) > 5 && $chunk != 'data: [DONE]' && isset(json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content)) {

                            $message = json_decode(substr($chunk, 6, strlen($chunk) - 6))->choices[0]->delta->content;

                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                            $output .= $messageFix;

                            $responsedText .= $message;
                            $total_used_tokens += countWords($message);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                            ob_flush();
                            flush();
                            usleep(5000);
                        }
                    }
                }
                $message = UserOpenaiChatMessage::whereId($message_id)->first();
                $chat = UserOpenaiChat::whereId($chat_id)->first();
                $message->response = $responsedText;
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->images = implode(',', $images);
                $message->pdfName = $request->pdfname;
                $message->pdfPath = $request->pdfpath;
                $message->save();
                $driver->input($responsedText)->calculateCredit()->decreaseCredit();
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
        } else {
            $chat = UserOpenaiChat::where('id', $request->chat_id)->first();
            $realtime = $request->realtime;
            $prompt = $request->prompt;
            $total_used_tokens = 0;
            $entry = new UserOpenaiChatMessage;
            $entry->user_id = Auth::id();
            $entry->user_openai_chat_id = $chat->id;
            $entry->input = $prompt;
            $entry->response = null;
            $entry->realtime = $realtime ?? 0;
            $entry->output = "(If you encounter this message, please attempt to send your message again. If the error persists beyond multiple attempts, please don't hesitate to contact us for assistance!)";
            $entry->hash = Str::random(256);
            $entry->credits = $total_used_tokens;
            $entry->words = 0;
            $entry->save();
            $chat->total_credits += $total_used_tokens;
            $chat->save();
            $chat_id = $chat->id;
            $message_id = $entry->id;

            return response()->json(compact('chat_id', 'message_id'));
        }
    }
}
