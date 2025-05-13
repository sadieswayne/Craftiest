<?php

namespace App\Http\Controllers;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Services\AnthropicService;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity as EntityFacade;
use App\Domains\Entity\Models\Entity;
use App\Enums\BedrockEngine;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\MarketplaceHelper;
use App\Models\Chatbot\Chatbot;
use App\Models\ChatBotHistory;
use App\Models\ChatCategory;
use App\Models\Favourite;
use App\Models\OpenaiGeneratorChatCategory;
use App\Models\PdfData;
use App\Models\RateLimit;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\User;
use App\Models\UserOpenaiChat;
use App\Models\UserOpenaiChatMessage;
use App\Services\Ai\OpenAI\FileSearchService;
use App\Services\Assistant\AssistantService;
use App\Services\Bedrock\BedrockRuntimeService;
use App\Services\Chatbot\ParserExcelService;
use App\Services\GatewaySelector;
use App\Services\VectorService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonException;
use OpenAI\Laravel\Facades\OpenAI;
use Random\RandomException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use ZipArchive;

class AIChatController extends Controller
{
    protected $client;

    protected $settings;

    protected $settings_two;

    protected BedrockRuntimeService $bedrockService;

    public function __construct(BedrockRuntimeService $bedrockService)
    {
        $this->bedrockService = $bedrockService;
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
        $apiKey = $this->getOpenAiApiKey(Auth::user());
        config(['openai.api_key' => $apiKey]);
    }

    private function firstOpenaiGeneratorChatCategory(?string $slug = null)
    {
        if ($slug) {
            return OpenaiGeneratorChatCategory::query()
                ->where('slug', $slug)
                ->first();
        }

        $userGenerator = OpenaiGeneratorChatCategory::query()
            ->whereNotIn('slug', ['ai_vision', 'ai_webchat', 'ai_pdf'])
            ->where('role', 'default')
            ->when(Auth::user()?->isUser(), function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('user_id')
                        ->orWhere('user_id', Auth::id());
                });
            })
            ->first();

        if ($userGenerator) {
            return $userGenerator;
        }

        return OpenaiGeneratorChatCategory::query()
            ->whereNotIn('slug', ['ai_vision', 'ai_webchat', 'ai_pdf'])
            ->where('role', 'default')
            ->firstOr(function () {
                return OpenaiGeneratorChatCategory::query()
                    ->whereNotIn('slug', ['ai_vision', 'ai_webchat', 'ai_pdf'])
                    ->first();
            });
    }

    public function search(Request $request): JsonResponse
    {
        $categoryId = $request->category_id;
        $search = $request->search_word;

        $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $categoryId)->where('is_chatbot', 0)->orderBy('updated_at', 'desc')->where('title', 'like', "%$search%");

        $list = $list->get();
        $html = view('panel.user.openai_chat.components.chat_sidebar_list', compact('list'))->render();

        return response()->json(compact('html'));
    }

    public function openAIChat($slug = null)
    {
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub !== null) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if ($activeSubY !== null) {
                $gateway = $activeSubY->paid_with;
            }
        }

        try {
            $isPaid = GatewaySelector::selectGateway($gateway)::getSubscriptionStatus();
        } catch (Exception $e) {
            $isPaid = false;
        }

        $category = $this->firstOpenaiGeneratorChatCategory($slug);

        if ($isPaid == false && $category->plan == 'premium' && auth()->user()->type !== 'admin') {
            // $aiList = OpenaiGeneratorChatCategory::all();
            $aiList = OpenaiGeneratorChatCategory::where('slug', '<>', 'ai_vision')->where('slug', '<>', 'ai_pdf')->get();
            $categoryList = ChatCategory::all();
            $favData = Favourite::where('type', 'chat')
                ->where('user_id', auth()->user()->id)
                ->get();
            $message = true;

            return redirect()->route('dashboard.user.openai.chat.chat')->with(compact('aiList', 'categoryList', 'favData', 'message'));
        }

        $list = $this->openai(\request())
            ->where('openai_chat_category_id', $category->id)
            ->where('is_chatbot', 0)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc');
        $list = $list->get();
        $chat = $list->first();
        $aiList = OpenaiGeneratorChatCategory::where('slug', '<>', 'ai_vision')->where('slug', '<>', 'ai_pdf')->get();
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
        if ($this->settings_two->openai_default_stream_server === 'frontend' || setting('realtime_voice_chat', 0)) {
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

        $apiSearch = base64_encode('https://google.serper.dev/search');
        $apiSearchId = base64_encode($this->settings_two->serper_api_key);
        $lastThreeMessage = null;
        $chat_completions = null;
        if ($chat !== null) {
            $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc')->take(2);
            $lastThreeMessage = $lastThreeMessageQuery->get()->reverse();
            $category = OpenaiGeneratorChatCategory::where('id', $chat->openai_chat_category_id)->first();
            $chat_completions = str_replace(["\r", "\n"], '', $category->chat_completions);

            if ($chat_completions) {
                $chat_completions = json_decode($chat_completions, true, 512, JSON_THROW_ON_ERROR);
            }
        }
        $chatbots = Chatbot::query()->get();
        $models = Entity::planModels();

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

        if ($slug === 'ai_realtime_voice_chat' && Helper::appIsDemo()) {
            foreach ($list as $chat) {
                $chat->messages()->delete();
                $chat->delete();
            }
        }

        return view('panel.user.openai_chat.chat', compact(
            'generators',
            'category',
            'apiSearch',
            'chatbots',
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
            'models',
        ));
    }

    protected function openai(Request $request): Builder
    {
        $team = $request->user()->getAttribute('team');
        $myCreatedTeam = $request->user()->getAttribute('myCreatedTeam');

        return UserOpenaiChat::query()
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

    /**
     * @throws Throwable
     * @throws RandomException
     */
    public function openChatAreaContainer(Request $request): JsonResponse
    {
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
        $chat = UserOpenaiChat::where('id', $request->chat_id)->first();
        $category = $chat->category;
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
        $html = view('panel.user.openai_chat.components.chat_area_container', compact(
            'chat',
            'category',
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'generators',
        ))->render();
        $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc')->take(2);
        $lastThreeMessage = $lastThreeMessageQuery->get()->toArray();

        return response()->json(compact('html', 'lastThreeMessage'));
    }

    public function openAIChatList()
    {
        abort_if(Helper::setting('feature_ai_chat') === 0, 404);

        $aiList = OpenaiGeneratorChatCategory::query()
            ->whereNotIn('slug', [
                'ai_vision', 'ai_webchat', 'ai_pdf',
            ])
            ->when(Auth::user()->isUser(), function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('user_id')->orWhere('user_id', Auth::id());
                });
            })
            ->get();

        $categoryList = ChatCategory::where('user_id', 1)->orWhere('user_id', auth()->user()->id)->get();
        $favData = Favourite::where('type', 'chat')
            ->where('user_id', auth()->user()->id)
            ->get();
        $message = false;

        return view('panel.user.openai_chat.list', compact('aiList', 'categoryList', 'favData', 'message'));
    }

    public function openChatBotArea(Request $request): JsonResponse
    {
        $chat = UserOpenaiChat::with(['messages' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->where('id', $request->chat_id)->first();

        $category = $chat?->category;
        $html = view('panel.user.openai_chat.components.chat_area', compact('chat', 'category'))->render();
        $lastThreeMessageQuery = $chat?->messages()->whereNot('input', null)->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc');
        $lastThreeMessage = $lastThreeMessageQuery->get()->toArray();

        return response()->json(compact('html', 'lastThreeMessage'));
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     * @throws RandomException
     */
    public function startNewChat(Request $request): JsonResponse
    {
        $user = Auth::user();
        $category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->firstOrFail();
        $chatbot = Chatbot::query()->where('id', $category->chatbot_id)->first();
        if ($category->assistant !== null) {
            $service = new AssistantService;
            $thread = $service->createThread();
        }
        $chat = new UserOpenaiChat;

        $chat->user_id = $user?->id;
        $chat->team_id = $user?->team_id;
        $chat->chatbot_id = $category->chatbot_id;
        $chat->openai_chat_category_id = $category->id;
        $chat->title = $category->name . ' Chat';
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->thread_id = $thread['id'] ?? null;
        $chat->save();

        $message = new UserOpenaiChatMessage;
        $message->user_openai_chat_id = $chat->id;
        $message->user_id = $user?->id;
        $message->response = 'First Initiation';
        if ($category->slug !== 'ai_vision' || $category->slug !== 'ai_pdf') {
            if ($category->role === 'default') {
                $output = __('Hi! I am') . ' ' . $category->name . __(', and I\'m here to answer all your questions');
            } else {
                $output = __('Hi! I am') . ' ' . $category->human_name . __(', and I\'m') . ' ' . $category->role . '. ' . $category->helps_with;
            }
        } else {
            $output = null;
        }

        if ($chatbot) {
            if ($chatbot->first_message !== null) {
                $output = $chatbot->first_message;
            }
        }

        if ($category) {
            if ($category->first_message !== null) {
                $output = $category->first_message;
            }
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
        $list = UserOpenaiChat::where('user_id', $user?->id)->where('openai_chat_category_id', $category->id)->where('is_chatbot', 0)->orderBy('updated_at', 'desc')->get();
        $html = view('panel.user.openai_chat.components.chat_area_container', compact(
            'chat',
            'category',
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'generators'
        ))->render();
        $html2 = view('panel.user.openai_chat.components.chat_sidebar_list', compact('list', 'chat', 'generators'))->render();

        return response()->json(compact('html', 'html2', 'chat'));
    }

    public function docToText($path_to_file): array|string|null
    {
        $fileHandle = fopen($path_to_file, 'rb');
        $line = @fread($fileHandle, filesize($path_to_file));
        $lines = explode(chr(0x0D), $line);
        $response = '';
        foreach ($lines as $current_line) {
            $pos = strpos($current_line, chr(0x00));
            if (($pos !== false) || ($current_line === '')) {
                $response .= "\n";
            } else {
                $response .= $current_line . ' ';
            }
        }

        return preg_replace('/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/', '', $response);
    }

    public function docxToText($path_to_file): bool|string
    {
        $response = '';
        $zip = new ZipArchive;
        if (! $zip->open($path_to_file)) {
            return false;
        }
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->statIndex($i);
            if ($entry['name'] !== 'word/document.xml') {
                continue;
            }
            $content = $zip->getFromIndex($i);

            $content = str_replace(['</w:r></w:p></w:tc><w:tc>', '</w:r></w:p>'], ["\r\n", "\n"], $content);
            $content = strip_tags($content);
            $response .= $content;
        }
        $zip->close();

        return $response;
    }

    /**
     * @throws JsonException
     */
    public function uploadDoc(Request $request, $chat_id, $type): JsonResponse|string
    {
        ApiHelper::setOpenAiKey();

        if ($type === 'application/pdf') {
            $type = 'pdf';
        } elseif ($type === 'application/msword') {
            $type = 'doc';
        } elseif ($type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $type = 'docx';
        } elseif ($type === 'text/csv') {
            $type = 'csv';
        } elseif ($type === 'application/vnd.ms-excel' || $type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $type = pathinfo($request->file('doc')->getClientOriginalName(), PATHINFO_EXTENSION);
        }

        $doc = $request->file('doc');

        $doc_content = file_get_contents($doc->getRealPath());
        $fileName = Str::random(12) . '.' . $type;
        Storage::disk('public')->put('temp.' . $type, $doc_content);
        Storage::disk('public')->put($fileName, $doc_content);

        $uploadedFile = new File(substr("/uploads/$fileName", 1));

        $resPath = "/uploads/$fileName";

        if ($this->settings_two->ai_image_storage === 's3') {
            try {
                $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                unlink(substr("/uploads/$fileName", 1));
                $resPath = Storage::disk('s3')->url($aws_path);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
            }
        }

        if ($type === 'pdf') {
            $parser = new \Smalot\PdfParser\Parser;
            $text = $parser->parseFile('uploads/temp.pdf')->getText();
            if (! mb_check_encoding($text, 'UTF-8')) {
                $page = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));
            } else {
                $page = $text;
            }
        } elseif ($type === 'docx') {
            $filePath = public_path('uploads/temp.' . $type);
            $page = $this->docxToText($filePath);
        } elseif ($type === 'doc') {
            $filePath = public_path('uploads/temp.' . $type);
            $page = $this->docToText($filePath);
        } elseif ($type === 'csv') {
            $file = file_get_contents(public_path('uploads/temp.' . $type));
            $rows = explode(PHP_EOL, $file);
            $header = str_getcsv(array_shift($rows)); // Get header row
            $dataAsJson = [];
            foreach ($rows as $row) {
                $data = array_combine($header, array_pad(str_getcsv($row), count($header), '')); // Combine header with data
                $dataAsJson[] = json_encode($data, JSON_THROW_ON_ERROR);
            }
            $page = implode("\n", $dataAsJson);
        } elseif ($type === 'xls' || $type === 'xlsx') {
            $filePath = public_path('uploads/temp.' . $type);

            $parser = app(ParserExcelService::class);

            $page = $parser->setPath($filePath)->parse();

        }

        $countwords = strlen($page) / 1001 + 1;
        $driver = EntityFacade::driver(EntityEnum::TEXT_EMBEDDING_ADA_002);
        for ($i = 0; $i < $countwords; $i++) {
            if (1001 * $i + 2000 > strlen($page)) {
                try {
                    $subtxt = substr($page, 1001 * $i, strlen($page) - 1001 * $i);
                    $subtxt = mb_convert_encoding($subtxt, 'UTF-8', 'UTF-8');
                    $subtxt = iconv('UTF-8', 'UTF-8//IGNORE', $subtxt);
                    $response = OpenAI::embeddings()->create([
                        'model' => $driver->enum()->value,
                        'input' => $subtxt,
                    ]);
                    if (strlen(substr($page, 1001 * $i)) > 10) {
                        $chatpdf = new PdfData;
                        $chatpdf->chat_id = $chat_id;
                        $chatpdf->content = substr($page, 1001 * $i, strlen($page) - 1001 * $i);
                        $chatpdf->vector = json_encode($response->embeddings[0]->embedding, JSON_THROW_ON_ERROR);
                        $chatpdf->save();
                    }
                } catch (Exception $e) {
                }
            } else {
                try {
                    $subtxt = substr($page, 1001 * $i, 2000);
                    $subtxt = mb_convert_encoding($subtxt, 'UTF-8', 'UTF-8');
                    $subtxt = iconv('UTF-8', 'UTF-8//IGNORE', $subtxt);
                    $response = OpenAI::embeddings()->create([
                        'model' => $driver->enum()->value,
                        'input' => $subtxt,
                    ]);
                    if (strlen(substr($page, 1001 * $i, 2000)) > 10) {
                        $chatpdf = new PdfData;

                        $chatpdf->chat_id = $chat_id;
                        $chatpdf->content = substr($page, 1001 * $i, 2000);
                        $chatpdf->vector = json_encode($response->embeddings[0]->embedding, JSON_THROW_ON_ERROR);

                        $chatpdf->save();
                    }
                } catch (Exception $e) {
                }
            }
            $driver->input($subtxt)->calculateCredit()->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());

        }

        return $resPath;
    }

    public function oldstartNewDocChat(Request $request): JsonResponse
    {
        $category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->firstOrFail();
        $chat = new UserOpenaiChat;
        $chat->user_id = Auth::id();
        $chat->team_id = Auth::user()->team_id;
        $chat->openai_chat_category_id = $category->id;
        $chat->title = $category->name . ' Chat';
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->save();

        try {
            $filePath = $this->uploadDoc($request, $chat->id, $request->type);
            $chat->reference_url = $filePath;
            $chat->doc_name = $request->file('doc')->getClientOriginalName();
            $chat->save();

            $message = new UserOpenaiChatMessage;
            $message->user_openai_chat_id = $chat->id;
            $message->user_id = Auth::id();
            $message->response = 'First Initiation';
            if ($category->slug !== 'ai_vision' || $category->slug !== 'ai_pdf') {
                if ($category->role === 'default') {
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

            $html = view('panel.user.openai_chat.components.chat_area_container', compact(
                'chat',
                'category',
                'generators',
                'apikeyPart1',
                'apikeyPart2',
                'apikeyPart3',
            ))->render();
            $html2 = view('panel.user.openai_chat.components.chat_sidebar_list', compact('list', 'chat'))->render();

            return response()->json(compact('html', 'html2', 'chat'));
        } catch (Exception $e) {
            $chat->delete();

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function startNewDocChat(Request $request): JsonResponse
    {
        $user = Auth::user();
        $category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->firstOrFail();
        $chat = new UserOpenaiChat;
        $chat->user_id = $user->id;
        $chat->team_id = $user->team_id;
        $chat->openai_chat_category_id = $category->id;
        $chat->title = $category->name . ' Chat';
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->save();

        try {
            $fileSearchService = new FileSearchService;
            $filePath = $this->storeFile($request, $request->type);
            $fileId = $fileSearchService->uploadFile($filePath);
            $vectors = $fileSearchService->createVectorStore(basename($filePath), $fileId);

            $chat->openai_vector_id = $vectors?->id;
            $chat->openai_file_id = $fileId;
            $chat->reference_url = $filePath;
            $chat->doc_name = $request->file('doc')?->getClientOriginalName();
            $chat->save();

            $message = new UserOpenaiChatMessage;
            $message->user_openai_chat_id = $chat->id;
            $message->user_id = Auth::id();
            $message->response = 'First Initiation';
            if ($category->slug !== 'ai_vision' || $category->slug !== 'ai_pdf') {
                if ($category->role === 'default') {
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

            $html = view('panel.user.openai_chat.components.chat_area_container', compact(
                'chat',
                'category',
                'generators',
                'apikeyPart1',
                'apikeyPart2',
                'apikeyPart3',
            ))->render();
            $html2 = view('panel.user.openai_chat.components.chat_sidebar_list', compact('list', 'chat'))->render();

            return response()->json(compact('html', 'html2', 'chat'));
        } catch (Exception|Throwable $e) {
            $chat->delete();

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store file to local storage
     */
    public function storeFile(Request $request, $type): string
    {
        $request->validate([
            'doc' => 'required|file|max:10240',
        ]);
        $mimeToExtension = [
            'text/x-c'                                                                  => 'c',
            'text/x-c++'                                                                => 'cpp',
            'text/x-csharp'                                                             => 'cs',
            'text/css'                                                                  => 'css',
            'application/msword'                                                        => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'text/x-golang'                                                             => 'go',
            'text/html'                                                                 => 'html',
            'text/x-java'                                                               => 'java',
            'text/javascript'                                                           => 'js',
            'application/json'                                                          => 'json',
            'text/markdown'                                                             => 'md',
            'application/pdf'                                                           => 'pdf',
            'text/x-php'                                                                => 'php',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'text/x-python'                                                             => 'py',
            'text/x-script.python'                                                      => 'py',
            'text/x-ruby'                                                               => 'rb',
            'application/x-sh'                                                          => 'sh',
            'text/x-tex'                                                                => 'tex',
            'application/typescript'                                                    => 'ts',
            'text/plain'                                                                => 'txt',
        ];
        if (! isset($mimeToExtension[$type])) {
            throw new InvalidArgumentException("Unsupported MIME type: $type");
        }
        $extension = $mimeToExtension[$type];
        $doc = $request->file('doc');
        if (str_starts_with($type, 'text/')) {
            $content = file_get_contents($doc->getRealPath());
            $encoding = mb_detect_encoding($content, ['UTF-8', 'UTF-16', 'ASCII'], true);
            if (! in_array($encoding, ['UTF-8', 'UTF-16', 'ASCII'])) {
                throw new InvalidArgumentException('Invalid encoding for text-based file. Must be UTF-8, UTF-16, or ASCII.');
            }
        }
        $fileName = $doc->hashName($extension);
        Storage::disk('public')->put($fileName, fopen($doc->getRealPath(), 'rb'));
        $resPath = public_path('/uploads/' . $fileName);
        if ($this->settings_two->ai_image_storage === 's3') {
            try {
                $aws_path = Storage::disk('s3')->put('', $doc);
                Storage::disk('public')->delete($fileName); // Clean up local file
                $resPath = Storage::disk('s3')->url($aws_path);
            } catch (Exception $e) {
                throw new RuntimeException('AWS Error - ' . $e->getMessage());
            }
        }

        return $resPath;
    }

    public function startNewChatBot(Request $request): JsonResponse
    {
        $chatbot = Chatbot::query()->where('id', $this->settings_two->chatbot_template)->firstOrFail();
        $category = $chatbot;
        $chat = new UserOpenaiChat;
        $chat->user_id = Auth::id();
        $chat->chatbot_id = $chatbot->id;
        //        $chat->openai_chat_category_id = $category->id;
        $chat->title = 'ChatBot';
        $chat->total_credits = 0;
        $chat->total_words = 0;
        $chat->is_chatbot = 1;
        $chat->save();

        $message = new UserOpenaiChatMessage;
        $message->user_openai_chat_id = $chat->id;
        $message->user_id = Auth::id();
        $message->response = 'First Initiation';
        $output = $category->first_message ?: 'How can I help you?';
        $message->output = $output;
        $message->hash = Str::random(256);
        $message->credits = 0;
        $message->words = 0;
        $message->is_chatbot = 1;
        $message->save();

        $chatbot_history = new ChatBotHistory;
        $chatbot_history->user_id = Auth::id();
        $chatbot_history->ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : request()->ip();
        $chatbot_history->user_openai_chat_id = $chat->id;
        $chatbot_history->openai_chat_category_id = $category->id;
        $chatbot_history->save();

        $html = view('panel.user.openai_chat.components.chat_area', compact('chat', 'category'))->render();

        return response()->json(compact('html', 'chat'));
    }

    /**
     * @throws JsonException
     * @throws GuzzleException
     */
    public function chatOutput(Request $request): StreamedResponse|JsonResponse
    {
        $user = Auth::user();
        $chat_id = $request->get('chat_id');
        $chat = UserOpenaiChat::whereId($chat_id)->first();

        if ($request->isMethod('post')) {
            $prompt = $request->get('prompt');
            // if ($chat->category->prompt_prefix != null && !str_starts_with($chat->category->slug, 'ai_')) {
            //     $prompt = "You will now play a character and respond as that character (You will never break character). Your name is". $chat->category->human_name. ". I want you to act as a". $chat->category->role . ". ". $chat->category->prompt_prefix . ' ' . $prompt;
            // }
            $realtime = $request->get('realtime');
            $total_used_tokens = 0;
            $entry = new UserOpenaiChatMessage;
            $entry->user_id = $user->id;
            $entry->user_openai_chat_id = $chat->id;
            $entry->input = $prompt;
            $entry->response = null;
            $entry->realtime = $realtime ?? 0;
            $entry->output = __("(If you encounter this message, please attempt to send your message again. If the error persists beyond multiple attempts, please don't hesitate to contact us for assistance!)");
            $entry->hash = Str::random(256);
            $entry->credits = $total_used_tokens;
            $entry->words = 0;
            $entry->save();
            $user?->save();
            $chat->total_credits += $total_used_tokens;
            $chat->save();
            $message_id = $entry->id;

            return response()->json(compact('message_id'));
        }
        if ($request->isMethod('get')) {
            $type = $request->get('type');
            if ($chat->category->slug === 'ai_pdf') {
                return $this->pdfStream($request);
            }
            if ($chat->category->slug === 'ai_webchat') {
                return $this->webChatStream($request);
            }

            return match ($type) {
                'vision' => $this->visionStream($request),
                default  => $this->chatbotsStream($request),
            };
        }

        return response()->json(['message' => 'Method not allowed'], 405);
    }

    /**
     * @throws JsonException
     */
    private function pdfStream(Request $request): StreamedResponse
    {
        $openaiApiKey = ApiHelper::setOpenAiKey();

        if (setting('default_ai_engine', EngineEnum::OPEN_AI->value) === EngineEnum::ANTHROPIC->value) {
            $openaiApiKey = ApiHelper::setAnthropicKey();
        }

        $chat_id = $request->get('chat_id');
        $message_id = $request->get('message_id');
        // $prompt = $request->get('prompt');
        $message = UserOpenaiChatMessage::whereId($message_id)->first();
        $prompt = $message->input;

        $chat_bot = $this->settings?->openai_default_model ?? EntityEnum::GPT_4_O->value;
        $history = [];

        if (setting('default_ai_engine', EngineEnum::OPEN_AI->value) === EngineEnum::ANTHROPIC->value) {
            $chat_bot = setting('anthropic_default_model', EntityEnum::CLAUDE_3_OPUS->value);
        }

        $chat = UserOpenaiChat::whereId($chat_id)->first();
        // check if there completions for the template
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
            $history[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
        }

        // follow the context of the last 5 messages
        $lastThreeMessageQuery = $chat->messages()
            ->whereNotNull('input')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->reverse();

        $vectorService = new VectorService;

        $extra_prompt = $vectorService->getMostSimilarText($prompt, $chat_id, 5, $chat->chatbot_id);
        $count = count($lastThreeMessageQuery);
        if ($count > 1) {
            $lastThreeMessageQuery[$count - 1]->input = "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. \n\n\n\n\nUser qusetion: $prompt \n\n\n\n\n Document Content: \n $extra_prompt";
            foreach ($lastThreeMessageQuery as $threeMessage) {
                $history[] = ['role' => 'user', 'content' => $threeMessage->input ?? ''];
                $output = $threeMessage->output;
                if ($output) {
                    $history[] = ['role' => 'assistant', 'content' => $output];
                } else {
                    $history[] = ['role' => 'assistant', 'content' => ''];
                }
            }
        } else {
            $history[] = ['role' => 'user', 'content' => "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. . User: $prompt \n\n\n\n\n Document Content: \n $extra_prompt"];
        }

        return $this->openaiChatStream($request, $openaiApiKey, $chat_bot, $history, $message_id);
    }

    private function chatbotsStream(Request $request)
    {
        $openaiApiKey = ApiHelper::setOpenAiKey();

        if (setting('default_ai_engine', EngineEnum::OPEN_AI->value) === EngineEnum::ANTHROPIC->value) {
            $openaiApiKey = ApiHelper::setAnthropicKey();
        }

        $chat_id = $request->get('chat_id');

        $message_id = $request->get('message_id');
        $message = UserOpenaiChatMessage::whereId($message_id)->first();
        $prompt = $message->input;

        $realtime = $request->get('realtime');
        $chat_bot = EntityEnum::fromSlug($this->settings?->openai_default_model);
        $history = [];
        $realtimePrompt = $prompt;
        $chat = UserOpenaiChat::whereId($chat_id)->first();
        // check if there completions for the template
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
            $history[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
        }

        if ($category && $category?->instructions) {
            $history[] = ['role' => 'system', 'content' => $category->instructions];
        }
        $extra_prompt = null;
        if ($category->chatbot_id) {
            try {
                $extra_prompt = (new VectorService)->getMostSimilarText($prompt, $chat_id, 2, $category->chatbot_id);
                if ($extra_prompt) {
                    $history[] = ['role' => 'user', 'content' => "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. . User: $prompt \n\n\n\n\n Document Content: \n $extra_prompt"];
                }
            } catch (Throwable $th) {
            }
        }

        // follow the context of the last 5 messages
        $lastThreeMessageQuery = $chat->messages()
            ->whereNotNull('input')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->reverse();

        $count = count($lastThreeMessageQuery);
        if ($count > 1) {
            foreach ($lastThreeMessageQuery as $threeMessage) {
                $history[] = ['role' => 'user', 'content' => $threeMessage->input ?? ''];
                if ($threeMessage->output !== null) {
                    $history[] = ['role' => 'assistant', 'content' => $threeMessage->output ?? ''];
                } else {
                    $history[] = ['role' => 'assistant', 'content' => ''];
                }
            }
            if ($realtime) {
                if (setting('default_realtime', 'serper') == 'serper' &&
                    ! is_null($this->settings_two->serper_api_key)) {
                    $driver = EntityFacade::driver(EntityEnum::SERPER);
                    $driver->redirectIfNoCreditBalance();

                    $sclient = new Client;
                    $headers = [
                        'X-API-KEY'    => $this->settings_two->serper_api_key,
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

                    $driver->input($realtimePrompt)->calculateCredit()->decreaseCredit();
                    Usage::getSingle()->updateWordCounts($driver->calculate());

                    $final_prompt =
                        'Prompt: ' . $realtimePrompt .
                        '\n\nWeb search json results: '
                        . json_encode($toGPT) .
                        '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';

                } elseif (MarketplaceHelper::isRegistered('perplexity') && setting('default_realtime') == 'perplexity' && ! is_null(setting('perplexity_key'))) {

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
                $history[] = ['role' => 'user', 'content' => $final_prompt ?? ''];
            } else {
                $history[] = ['role' => 'user', 'content' => $prompt ?? ''];
            }
        } else {
            if ($realtime) {
                if (setting('default_realtime', 'serper') == 'serper' &&
                    ! is_null($this->settings_two->serper_api_key)) {
                    $driver = EntityFacade::driver(EntityEnum::SERPER);
                    $driver->redirectIfNoCreditBalance();

                    $sclient = new Client;
                    $headers = [
                        'X-API-KEY'    => $this->settings_two->serper_api_key,
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
                    $driver->input($realtimePrompt)->calculateCredit()->decreaseCredit();
                    Usage::getSingle()->updateWordCounts($driver->calculate());

                    $final_prompt =
                        'Prompt: ' . $realtimePrompt .
                        '\n\nWeb search json results: '
                        . json_encode($toGPT) .
                        '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';

                } elseif (MarketplaceHelper::isRegistered('perplexity') && setting('default_realtime') == 'perplexity' &&
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
                $history[] = ['role' => 'user', 'content' => $final_prompt ?? ''];
            } else {
                $history[] = ['role' => 'user', 'content' => $prompt ?? ''];
            }
        }

        return self::openaiChatStream($request, $openaiApiKey, $chat_bot, $history, $message_id, null, [], $category);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    private function webChatStream(Request $request): StreamedResponse
    {
        $openaiApiKey = ApiHelper::setOpenAiKey();

        if (setting('default_ai_engine', EngineEnum::OPEN_AI->value) === EngineEnum::ANTHROPIC->value) {
            $openaiApiKey = ApiHelper::setAnthropicKey();
        }
        $chat_id = $request->get('chat_id');
        $message_id = $request->get('message_id');
        $message = UserOpenaiChatMessage::whereId($message_id)->first();
        $prompt = $message->input;
        $realtime = $request->get('realtime');
        $chat_bot = $this->settings?->openai_default_model;
        $history = [];
        $realtimePrompt = $prompt;

        $chat = UserOpenaiChat::whereId($chat_id)->first();
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
            $history[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
        }

        if ($category && $category?->instructions) {
            $history[] = ['role' => 'system', 'content' => $category->instructions];
        }

        try {
            $extra_prompt = (new VectorService)->getMostSimilarText($prompt, $chat_id, 2);
            if ($extra_prompt) {
                $history[] = ['role' => 'user', 'content' => "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. . User: $prompt \n\n\n\n\n Document Content: \n $extra_prompt"];
            }
        } catch (Throwable $th) {

        }

        // follow the context of the last 5 messages
        $lastThreeMessageQuery = $chat->messages()
            ->whereNotNull('input')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->reverse();

        $count = count($lastThreeMessageQuery);
        if ($count > 1) {
            foreach ($lastThreeMessageQuery as $threeMessage) {
                $history[] = ['role' => 'user', 'content' => $threeMessage->input ?? ''];
                if ($threeMessage->output !== null) {
                    $history[] = ['role' => 'assistant', 'content' => $threeMessage->output ?? ''];
                } else {
                    $history[] = ['role' => 'assistant', 'content' => ''];
                }
            }
            if ($realtime) {
                if (setting('default_realtime', 'serper') == 'serper' &&
                    ! is_null($this->settings_two->serper_api_key)) {
                    $driver = EntityFacade::driver(EntityEnum::SERPER);
                    $driver->redirectIfNoCreditBalance();

                    $sclient = new Client;
                    $headers = [
                        'X-API-KEY'    => $this->settings_two->serper_api_key,
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
                    $driver->input($realtimePrompt)->calculateCredit()->decreaseCredit();
                    Usage::getSingle()->updateWordCounts($driver->calculate());

                    $final_prompt =
                        'Prompt: ' . $realtimePrompt .
                        '\n\nWeb search json results: '
                        . json_encode($toGPT) .
                        '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';

                } elseif (MarketplaceHelper::isRegistered('perplexity') && setting('default_realtime') == 'perplexity' &&
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
                $history[] = ['role' => 'user', 'content' => $final_prompt ?? ''];
            } else {
                $history[] = ['role' => 'user', 'content' => $prompt ?? ''];
            }
        } elseif ($realtime) {
            if (setting('default_realtime', 'serper') == 'serper' &&
                ! is_null($this->settings_two->serper_api_key)) {
                $driver = EntityFacade::driver(EntityEnum::SERPER);
                $driver->redirectIfNoCreditBalance();

                $sclient = new Client;
                $headers = [
                    'X-API-KEY'    => $this->settings_two->serper_api_key,
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
                $driver->input($realtimePrompt)->calculateCredit()->decreaseCredit();
                Usage::getSingle()->updateWordCounts($driver->calculate());

                $final_prompt =
                    'Prompt: ' . $realtimePrompt .
                    '\n\nWeb search json results: '
                    . json_encode($toGPT) .
                    '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';

            } elseif (MarketplaceHelper::isRegistered('perplexity') && setting('default_realtime') == 'perplexity' &&
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

                    } else {
                        return response()->stream(function () use ($response) {
                            echo 'data: ' . json_encode([
                                'status'  => 'error',
                                'message' => $response->body(),
                            ], JSON_THROW_ON_ERROR);
                            echo "\n\n";
                            flush();
                        });
                    }
                } catch (Exception $e) {
                    return response()->stream(function () use ($e) {
                        echo 'data: ' . json_encode([
                            'status'  => 'error',
                            'message' => $e->getMessage(),
                        ], JSON_THROW_ON_ERROR);
                        echo "\n\n";
                        flush();
                    });
                }
            }
            $history[] = ['role' => 'user', 'content' => $final_prompt ?? ''];
        } else {
            $history[] = ['role' => 'user', 'content' => $prompt ?? ''];
        }

        return $this->openaiChatStream($request, $openaiApiKey, $chat_bot, $history, $message_id, null, [], $category);
    }

    private function visionStream(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $openaiApiKey = $this->getOpenAiApiKey($user);
        $chat_id = $request->get('chat_id');

        $message_id = $request->get('message_id');
        $message = UserOpenaiChatMessage::whereId($message_id)->first();
        $prompt = $message->input;
        $chat_bot = EntityEnum::GPT_4_O->value;
        $history = [];

        $chat = UserOpenaiChat::whereId($chat_id)->first();
        $history[] = [
            'role'    => 'system',
            'content' => 'You will now play a character and respond as that character (You will never break character). Your name is Vision AI. Must not introduce by yourself as well as greetings. Help also with asked questions based on previous responses and images if exists.',
        ];
        $lastThreeMessageQuery = $chat->messages()
            ->whereNotNull('input')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->reverse();
        $images = explode(',', $request->images);
        $count = count($lastThreeMessageQuery);
        if ($count > 1) {
            foreach ($lastThreeMessageQuery as $threeMessage) {
                $history[] = [
                    'role'    => 'user',
                    'content' => array_merge(
                        [
                            [
                                'type' => 'text',
                                'text' => $threeMessage->input,
                            ],
                        ],
                        collect($threeMessage->images)->map(static function ($item) {
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
                                    $imageResults[] = [
                                        'type'      => 'image_url',
                                        'image_url' => [
                                            'url' => 'data:image/png;base64,' . $base64Image,
                                        ],
                                    ];
                                }
                            }

                            return $imageResults;
                        })->reject(fn ($value) => empty($value))->flatten(1)->toArray()
                    ),
                ];
                if ($threeMessage->response !== null) {
                    $history[] = ['role' => 'assistant', 'content' => $threeMessage->response];
                }
            }
        }
        $history[] =
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
                            $imageData = file_get_contents(substr($item, 1));
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
            ];

        return $this->openaiChatStream($request, $openaiApiKey, $chat_bot, $history, $message_id, 2000, $images);
    }

    private function openaiChatStream($request, $openaiApiKey, string $chat_bot, $history, $message_id, $ai_max_tokens = null, $images = [], $category = null): StreamedResponse
    {
        $driver = EntityFacade::driver(EntityEnum::fromSlug($chat_bot));

        return response()->stream(function () use ($request, $openaiApiKey, $ai_max_tokens, $history, $driver, $message_id, $images, $category) {
            if (! $driver->hasCreditBalance()) {
                echo PHP_EOL;
                echo "event: data\n";
                echo 'data: ' . __('You have no credits left. Please buy more credits to continue.');
                echo "\n\n";
                flush();
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                flush();

                return null;
            }
            $openaiUse = setting('default_ai_engine', EngineEnum::OPEN_AI->value) === EngineEnum::OPEN_AI->value;
            if ($category && $category?->chatbot_id) {
                $openaiUse = true;
            }
            if ($ai_max_tokens !== null) {
                if ($openaiUse) {
                    $gclient = new Client;
                    $url = 'https://api.openai.com/v1/chat/completions';
                    $headers = [
                        'Authorization' => 'Bearer ' . $openaiApiKey,
                    ];

                    $postData = [
                        'headers' => $headers,
                        'json'    => [
                            'model'      => $driver->enum()->value,
                            'messages'   => $history,
                            'max_tokens' => $ai_max_tokens,
                            'stream'     => true,
                        ],
                    ];

                    $response = $gclient->post($url, $postData);
                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';

                    foreach (explode("\n", $response->getBody()->getContents()) as $chunk) {
                        if (strlen($chunk) > 5 && $chunk !== 'data: [DONE]' && isset(json_decode(substr($chunk, 6), false, 512, JSON_THROW_ON_ERROR)->choices[0]->delta->content)) {

                            $message = json_decode(substr($chunk, 6), false, 512, JSON_THROW_ON_ERROR)->choices[0]->delta->content;

                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                            $output .= $messageFix;

                            $responsedText .= $message;
                            $total_used_tokens += countWords($message);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                            flush();
                            usleep(1000);
                        }
                    }
                } else {
                    $historyMessages = array_filter($history, function ($item) {
                        return $item['role'] !== 'system';
                    });
                    $system = Arr::first(array_filter($history, function ($item) {
                        return $item['role'] === 'system';
                    }));

                    foreach ($historyMessages as $message) {
                        if (isset($message['content'])) {
                            $content = $message['content'];
                        }
                    }

                    if (setting('anthropic_default_model') === BedrockEngine::BEDROCK->value) {
                        $responseBody = $this->bedrockService->invokeClaude($content);
                        $completion = $responseBody['completion'];
                        foreach (explode("\n", $completion) as $chunk) {
                            $words = explode(' ', $chunk);
                            foreach ($words as $word) {
                                $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $word) . ' ';
                                $output .= $messageFix;
                                $responsedText .= $word . ' ';

                                $total_used_tokens += countWords($word);
                                $string_length = Str::length($messageFix);
                                $needChars = 6000 - $string_length;
                                $random_text = Str::random($needChars);

                                echo PHP_EOL;
                                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                                flush();
                                usleep(1000);
                            }
                            if (connection_aborted()) {
                                break;
                            }
                        }

                    } else {
                        $client = app(AnthropicService::class);

                        $system = data_get($system, 'content');

                        $data = $client->setStream(true)
                            ->setSystem($system)
                            ->setMessages(array_values($historyMessages))
                            ->stream()
                            ->body();

                        $total_used_tokens = 0;
                        $output = '';
                        $responsedText = '';

                        foreach (explode("\n", $data) as $chunk) {

                            if (strlen($chunk) < 6) {
                                continue;
                            }

                            if (! Str::contains($chunk, 'data: ')) {

                                continue;
                            }

                            $chunk = str_replace('data: {', '{', $chunk);

                            if (isset(json_decode($chunk, false, 512, JSON_THROW_ON_ERROR)->delta->text)) {
                                $message = json_decode($chunk, false, 512, JSON_THROW_ON_ERROR)->delta->text;

                                $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);

                                $output .= $messageFix;
                                $responsedText .= $message;

                                $total_used_tokens += countWords($message);
                                $string_length = Str::length($messageFix);
                                $needChars = 6000 - $string_length;
                                $random_text = Str::random($needChars);

                                echo PHP_EOL;
                                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                                flush();
                                usleep(1000);
                            }
                            if (connection_aborted()) {
                                break;
                            }
                        }
                    }
                }
            } elseif ($openaiUse) {
                $stream = OpenAI::chat()->createStreamed([
                    'model'             => $driver->enum()->value,
                    'messages'          => $history,
                    'presence_penalty'  => 0.6,
                    'frequency_penalty' => 0,
                ]);
                $total_used_tokens = 0;
                $output = '';
                $responsedText = '';
                foreach ($stream as $response) {
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
                        flush();
                        // ob_flush();
                        usleep(1000);
                    }
                    if (connection_aborted()) {
                        break;
                    }
                }
            } else {
                $historyMessages = array_filter($history, function ($item) {
                    return $item['role'] !== 'system';
                });
                $system = Arr::first(array_filter($history, function ($item) {
                    return $item['role'] === 'system';
                }));
                foreach ($historyMessages as $message) {
                    if (isset($message['content'])) {
                        $content = $message['content'];
                    }
                }

                if (setting('anthropic_default_model') === BedrockEngine::BEDROCK->value) {
                    $responseBody = $this->bedrockService->invokeClaude($content);
                    $completion = $responseBody['completion'];
                    foreach (explode("\n", $completion) as $chunk) {
                        $words = explode(' ', $chunk);
                        foreach ($words as $word) {
                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $word) . ' ';
                            $output .= $messageFix;
                            $responsedText .= $word . ' ';

                            $total_used_tokens += countWords($word);
                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                            flush();
                            usleep(1000);
                        }
                        if (connection_aborted()) {
                            break;
                        }
                    }
                } else {
                    $client = app(AnthropicService::class);
                    $system = data_get($system, 'content');
                    $data = $client->setStream(true)
                        ->setSystem($system)
                        ->setMessages(array_values($historyMessages))
                        ->stream()
                        ->body();
                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';
                    foreach (explode("\n", $data) as $chunk) {
                        if (strlen($chunk) < 6) {
                            continue;
                        }

                        if (! Str::contains($chunk, 'data: ')) {
                            continue;
                        }

                        $chunk = str_replace('data: {', '{', $chunk);
                        if (isset(json_decode($chunk)->delta->text)) {
                            $message = json_decode($chunk)->delta->text;

                            $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);

                            $output .= $messageFix;
                            $responsedText .= $message;

                            $total_used_tokens += countWords($message);
                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);

                            echo PHP_EOL;
                            echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                            flush();
                            usleep(1000);
                        }

                        if (connection_aborted()) {
                            break;
                        }
                    }
                }
            }
            $message = UserOpenaiChatMessage::whereId($message_id)->first();
            $chat_id = $message->user_openai_chat_id;
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
            $driver
                ->input($responsedText)
                ->calculateCredit()
                ->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());

            $chat->total_credits += $total_used_tokens;
            $chat->save();
            echo 'data: [DONE]';
            echo "\n\n";
            flush();
            usleep(1000);
        }, 200, [
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type'      => 'text/event-stream',
        ]);

    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function chatbotOutput(Request $request): StreamedResponse|JsonResponse
    {
        $chatbot = Chatbot::where('id', $this->settings_two->chatbot_template)->firstOrFail();

        if ($request->isMethod('get')) {

            $type = $request->type;
            $images = explode(',', $request->images);
            $model = $chatbot->model;
            $chat_id = $request->chat_id;
            $message_id = $request->message_id;
            $message = UserOpenaiChatMessage::whereId($message_id)->first();
            $prompt = $message->input;
            $realtime = $message->realtime;
            $realtimePrompt = $prompt;
            $chat = UserOpenaiChat::whereId($chat_id)->first();

            $lastThreeMessageQuery = $chat->messages()
                ->whereNotNull('input')
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get()
                ->reverse();
            $i = 0;

            $history[] = ['role' => 'system', 'content' => $chatbot->instructions ?: 'You are a helpful assistant.'];

            $vectorService = new VectorService;

            $extra_prompt = $vectorService->getMostSimilarText(
                $prompt,
                $chat_id,
                5,
                $this->settings_two->chatbot_template
            );

            if (count($lastThreeMessageQuery) > 1 && ! $realtime) {
                if ($extra_prompt !== '') {
                    $lastThreeMessageQuery[count($lastThreeMessageQuery) - 1]->input = "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. \n\n\n\n\nUser qusetion: $prompt \n\n\n\n\n Document Content: \n $extra_prompt";
                }

                foreach ($lastThreeMessageQuery as $threeMessage) {
                    $history[] = ['role' => 'user', 'content' => $threeMessage->input];
                    if ($threeMessage->response !== null) {
                        $history[] = ['role' => 'assistant', 'content' => $threeMessage->response];
                    }
                }
            } elseif ($extra_prompt === '') {
                if ($realtime) {
                    if (setting('default_realtime', 'serper') == 'serper' &&
                        ! is_null($this->settings_two->serper_api_key)) {
                        $driver = EntityFacade::driver(EntityEnum::SERPER);
                        $driver->redirectIfNoCreditBalance();

                        $sclient = new Client;
                        $headers = [
                            'X-API-KEY'    => $this->settings_two->serper_api_key,
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

                        $driver->input($realtimePrompt)->calculateCredit()->decreaseCredit();
                        Usage::getSingle()->updateWordCounts($driver->calculate());

                        $final_prompt =
                            'Prompt: ' . $realtimePrompt .
                            '\n\nWeb search json results: '
                            . json_encode($toGPT) .
                            '\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.';

                    } elseif (MarketplaceHelper::isRegistered('perplexity') && setting('default_realtime') == 'perplexity' &&
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
                    $history[] = ['role' => 'user', 'content' => $final_prompt ?? ''];
                } else {
                    $history[] = ['role' => 'user', 'content' => $prompt ?? ''];
                }
            } else {
                $history[] = ['role' => 'user', 'content' => "'this file' means file content. Must not reference previous chats if user asking about pdf. Must reference file content if only user is asking about file content. Else just response as an assistant shortly and professionaly without must not referencing file content. . User: $prompt \n\n\n\n\n Document Content: \n $extra_prompt"];
            }

            $driver = EntityFacade::driver(EntityEnum::fromSlug($model));

            return response()->stream(function () use ($prompt, $request, $chat_id, $message_id, $history, $driver, $type, $images) {
                if (! $driver->hasCreditBalance()) {
                    echo PHP_EOL;
                    echo "event: data\n";
                    echo 'data: ' . __('You have no credits left. Please buy more credits to continue.');
                    echo "\n\n";
                    flush();
                    echo "event: stop\n";
                    echo 'data: [DONE]';
                    echo "\n\n";
                    flush();

                    return null;
                }
                if ($type === 'chat') {
                    try {
                        $stream = OpenAI::chat()->createStreamed([
                            'model'             => $driver->enum()->value,
                            'messages'          => $history,
                            'presence_penalty'  => 0.6,
                            'frequency_penalty' => 0,
                        ]);
                        $total_used_tokens = 0;
                        $output = '';
                        $responsedText = '';
                        foreach ($stream as $response) {

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
                                flush();
                                usleep(5000);
                            }
                            if (connection_aborted()) {
                                break;
                            }
                        }
                    } catch (Exception $exception) {
                        $output = '';
                        $total_used_tokens = 0;
                        $responsedText = $exception->getMessage();
                        $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message ' . $exception->getMessage();
                        echo "data: $messageError";
                        echo "\n\n";
                        flush();
                        echo 'data: [DONE]';
                        echo "\n\n";
                        flush();
                        usleep(50000);
                    }
                } elseif ($type === 'vision') {
                    try {
                        $driver = EntityFacade::driver(EntityEnum::GPT_4_O);
                        $gclient = new Client;
                        $openaiApiKey = $this->getOpenAiApiKey(Auth::user());
                        $url = 'https://api.openai.com/v1/chat/completions';

                        $response = $gclient->post(
                            $url,
                            [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $openaiApiKey,
                                ],
                                'json' => [
                                    'model'    => $driver->enum()->value,
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
                                    'max_tokens' => 2000,
                                    'stream'     => true,
                                ],
                            ],
                        );
                    } catch (Exception $exception) {
                        $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message ' . $exception->getMessage();
                        echo "data: $messageError";
                        echo "\n\n";
                        // ob_flush();
                        flush();
                        echo 'data: [DONE]';
                        echo "\n\n";
                        // ob_flush();
                        flush();
                        usleep(50000);
                    }

                    $total_used_tokens = 0;
                    $output = '';
                    $responsedText = '';

                    foreach (explode("\n", $response->getBody()->getContents()) as $chunk) {
                        if (strlen($chunk) > 5 && $chunk !== 'data: [DONE]' && isset(json_decode(substr($chunk, 6, strlen($chunk) - 6), false, 512, JSON_THROW_ON_ERROR)->choices[0]->delta->content)) {

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
                $driver
                    ->input($responsedText)
                    ->calculateCredit()
                    ->decreaseCredit();
                Usage::getSingle()->updateWordCounts($driver->calculate());
                $chat->total_credits += $total_used_tokens;
                $chat->save();
                echo 'data: [DONE]';
                echo "\n\n";
                flush();
                usleep(50000);
            }, 200, [
                'Cache-Control'     => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type'      => 'text/event-stream',
            ]);
        }

        $ipAddress = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? request()->ip();
        $db_ip_address = RateLimit::where('ip_address', $ipAddress)->where('type', 'chatbot')->first();
        if ($db_ip_address) {
            if (now()->diffInDays(Carbon::parse($db_ip_address->last_attempt_at)->format('Y-m-d')) > 0) {
                $db_ip_address->attempts = 0;
            }
        } else {
            $db_ip_address = new RateLimit(['ip_address' => $ipAddress, 'type' => 'chatbot']);
        }

        if ($db_ip_address->attempts >= $this->settings_two->chatbot_rate_limit) {
            $data = [
                'errors' => [__('You have reached the maximum number of ask to chatbot allowed.')],
            ];

            return response()->json($data, 429);
        }

        $db_ip_address->attempts++;
        $db_ip_address->last_attempt_at = now();
        $db_ip_address->save();

        $chat = UserOpenaiChat::where('id', $request->chat_id)->first();
        $realtime = $request->realtime;
        $user = Auth::user();
        $total_used_tokens = 0;
        $entry = new UserOpenaiChatMessage;
        $entry->user_id = $user?->id;
        $entry->user_openai_chat_id = $chat->id;
        $entry->is_chatbot = 1;
        $entry->input = $request->prompt;
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

    public function transAudio(Request $request): JsonResponse
    {
        $user = Auth::user();
        $driver = EntityFacade::driver(EntityEnum::WHISPER_1);
        $driver->redirectIfNoCreditBalance();

        $file = $request->file('file');
        $path = 'uploads/audio/';
        $file_name = Str::random(4) . '-' . Str::slug($user?->fullName()) . '-audio.' . $file->getClientOriginalExtension();

        // Audio Extension Control
        $imageTypes = ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];
        if (! in_array(Str::lower($file->getClientOriginalExtension()), $imageTypes)) {
            $data = [
                'errors' => ['Invalid extension, accepted extensions are mp3, mp4, mpeg, mpga, m4a, wav, and webm.'],
            ];

            return response()->json($data, 419);
        }

        try {

            $file->move($path, $file_name);
            $response = OpenAI::audio()->transcribe([
                'file'            => fopen($path . $file_name, 'rb'),
                'model'           => $driver->enum()->value,
                'response_format' => 'verbose_json',
            ]);

            unlink($path . $file_name);
            $text = $response->text;
            $driver->input($text)->calculateCredit()->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());

        } catch (Exception $e) {
            $text = '';
        }

        return response()->json($text);
    }

    public function deleteChat(Request $request): void
    {
        $chat_id = explode('_', $request->chat_id)[1];
        $chat = UserOpenaiChat::where('id', $chat_id)->first();
        $chat->delete();
    }

    public function clearChats(Request $request): JsonResponse
    {
        // clear all chats for related chat category slug
        if (Helper::appIsNotDemo()) {
            $user = Auth::user();
            $category_id = $request->category_id;
            $chats = UserOpenaiChat::where('user_id', $user->id)->where('openai_chat_category_id', $category_id)->get();
            if ($chats) {
                foreach ($chats as $chat) {
                    $chat->delete();
                }
            }
        }

        return response()->json(['error' => __('This action is disabled in the demo.')]);
    }

    public function renameChat(Request $request): void
    {
        $chat_id = explode('_', $request->chat_id)[1];
        $chat = UserOpenaiChat::where('id', $chat_id)->first();
        if ($chat) {
            $chat->title = $request->title;
            $chat->save();
        }
    }

    public function pinConversation(Request $request): void
    {
        $chat_id = explode('_', $request->chat_id)[1];
        $chat = UserOpenaiChat::where('id', $chat_id)->first();
        if ($chat) {
            $chat->is_pinned = $request->pinned;
            $chat->save();
        }
    }

    // Low
    public function lowChatSave(Request $request): JsonResponse
    {
        $chat = UserOpenaiChat::find($request->chat_id);
        $chat_bot = EntityEnum::fromSlug(empty($request->model) ? $this->settings?->openai_default_model : $request->model) ?? EntityEnum::GPT_4_O;

        $message = new UserOpenaiChatMessage;
        $message->user_openai_chat_id = $chat->id;
        $message->user_id = Auth::id();
        $message->input = $request->input;
        $message->response = $request->response;
        $message->output = $request->response;
        $message->hash = Str::random(256);
        $message->credits = countWords($request->response);
        $message->words = countWords($request->response);
        $message->images = $request->images;
        $message->pdfPath = $request->pdfPath;
        $message->pdfName = $request->pdfName;
        $message->outputImage = $request->outputImage;
        $message->save();
        if (! empty($chat->category->slug) && $chat->category->slug === 'ai_chat_image') {
            $chat_bot = $this->getDefaultOpenAiImageModel();
            $driver = EntityFacade::driver($chat_bot)->inputImageCount(1);
            Usage::getSingle()->updateImageCounts($driver->calculate());
        } else {
            $driver = EntityFacade::driver($chat_bot)->input($request->response);
            Usage::getSingle()->updateWordCounts($driver->calculate());
            $driver->calculateCredit()->decreaseCredit();
        }

        return response()->json([]);
    }

    public function changeChatTitle(Request $request): JsonResponse
    {
        $changed = false;
        $streamed_message_id = $request->streamed_message_id;
        $message = UserOpenaiChatMessage::whereId($streamed_message_id)->first();
        $chat_id = $message->user_openai_chat_id;
        $chat = UserOpenaiChat::whereId($chat_id)->first();

        $newTitle = '';
        $chat_bot = EntityEnum::fromSlug($this->settings?->openai_default_model) ?? EntityEnum::GPT_3_5_TURBO;
        if ($chat->messages()->count() <= 2) {
            $systemPromot = $this->applyPromptRules('You are a chatbot. Generate a title for a chat based on provided conversation. You must return a title only.');
            $generatedNewChatTitle = OpenAI::chat()->create([
                'model'    => $chat_bot->value,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => $systemPromot,
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

    private function getOpenAiApiKey(?User $user): string
    {
        return ApiHelper::setOpenAiKey();
    }

    private function applyPromptRules(string $prompt): string
    {
        $prompt .= '
        here some rules you must follow:
            1. Dont use double quotation ".." marks in answers.
        ';

        return $prompt;
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
}
