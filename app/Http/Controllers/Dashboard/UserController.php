<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\CreateActivity;
use App\Console\Commands\FluxProQueueCheck;
use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Models\Entity;
use App\Enums\Plan\FrequencyEnum;
use App\Enums\Plan\TypeEnum;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\MarketplaceHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Finance\PaymentProcessController;
use App\Jobs\SendInviteEmail;
use App\Models\AccountDeletionReqs;
use App\Models\Currency;
use App\Models\Folders;
use App\Models\Gateways;
use App\Models\Integration\Integration;
use App\Models\Integration\UserIntegration;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorChatCategory;
use App\Models\OpenaiGeneratorFilter;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Team\Team;
use App\Models\User;
use App\Models\UserAffiliate;
use App\Models\UserDocsFavorite;
use App\Models\UserFavorite;
use App\Models\UserOpenai;
use App\Models\UserOpenaiChat;
use App\Models\UserOrder;
use App\Models\Voice\ElevenlabVoice;
use App\Services\Dashboard\UserDashboardService;
use App\Services\ElevenlabsService;
use App\Services\GatewaySelector;
use App\Services\Orders\OrdersExportService;
use enshrined\svgSanitize\Sanitizer;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Throwable;

class UserController extends Controller
{
    public function __construct(
        public UserDashboardService $service,
    ) {}

    public function checkStatus(Request $request)
    {
        $data = UserOpenai::query()
            ->where('status', 'IN_QUEUE')
            ->where('response', 'FL')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([]);
        }

        FluxProQueueCheck::updateFluxProImages();

        return [
            'data' => UserOpenai::query()
                ->whereIn('id', $data->pluck('id')->toArray())
                ->where('status', '<>', 'IN_QUEUE')
                ->get()
                ->map(function ($item) {
                    $item->setAttribute('imgId', 'img-' . $item->response . '-' . $item->id);
                    $item->setAttribute('payloadId', 'img-' . $item->response . '-' . $item->id . '-payload');
                    $item->setAttribute('img', ThumbImage($item->output));

                    return $item;
                }),

        ];
    }

    public function redirect(Request $request)
    {
        $route = 'dashboard.user.index';

        if ($request->user()->isAdmin() && Helper::appIsNotDemo()) {
            $route = 'dashboard.admin.index';
        }

        return to_route($route);
    }

    public function index()
    {
        $this->service->setCache();

        $isNotDemo = Helper::appIsNotDemo();
        $ongoingPayments = null;
        // $ongoingPayments = self::prepareOngoingPaymentsWarning();
        // $user = Auth::user();
        $team = null;
        if ($isNotDemo) {
            PaymentProcessController::checkUnmatchingSubscriptions();
        }

        $team = $this->getTeam(Auth::user());

        return view('panel.user.dashboard', [
            'team'              => $team,
            'ongoingPayments'   => $ongoingPayments,
            'recently_launched' => UserOpenai::query()
                ->where('user_id', Auth::id())
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }

    public function markTourSeen()
    {
        $user = Auth::user();
        $user->tour_seen = true;
        $user->save();

        return response()->json(['status' => 'success']);
    }

    public function getTeam(User $user)
    {
        if ($team = $user->myCreatedTeam) {

            if ($team->allow_seats != $user?->relationPlan?->plan_allow_seat) {
                $team->allow_seats = $user?->relationPlan?->plan_allow_seat ?: 0;
                $team->save();
            }

            return $team;
        }

        $allow_seats = $user->isAdmin() ? 100 : $user?->relationPlan?->plan_allow_seat;

        return Team::query()->firstOrCreate([
            'user_id' => auth()->id(),
        ], [
            'name'        => $user?->fullName(),
            'allow_seats' => $allow_seats ?: 0,
        ]);
    }

    public function prepareOngoingPaymentsWarning()
    {
        $ongoingPayments = PaymentProcessController::checkForOngoingPayments();

        if ($ongoingPayments) {
            return $ongoingPayments;
        }

        return null;
    }

    public function openAIList()
    {
        abort_if(Helper::setting('feature_ai_writer') == 0, 404);

        return view('panel.user.openai.list', [
            'list' => OpenAIGenerator::query()
                ->where(function ($query) {
                    $query->where('user_id', Auth::id())
                        ->orWhereNull('user_id');
                })->where('active', true)->get(),
            'filters' => OpenaiGeneratorFilter::query()->where(function ($query) {
                $query->where('user_id', auth()->user()->id)
                    ->orWhereNull('user_id');
            })->orderBy('name', 'desc')->get(),
        ]);
    }

    public function openAICustomList()
    {
        abort_if(setting('user_ai_writer_custom_templates', 0) == 0, 404);

        $list = OpenAIGenerator::query()
            ->with('user')
            ->orderBy('title', 'asc')
            ->where('custom_template', 1)
            ->where('user_id', Auth::id())
            ->get();

        return view('panel.user.openai.custom.list', compact('list'));
    }

    public function openAICustomAddOrUpdateSave(Request $request)
    {
        $userId = Auth::id();
        if ($request->template_id != 'undefined') {
            $template = OpenAIGenerator::where('id', $request->template_id)->where('user_id', $userId)->firstOrFail();
        } else {
            $template = new OpenAIGenerator;
        }

        // Set basic template attributes
        $template->title = $request->title;
        $template->description = $request->description;
        $template->image = $request->image;
        $template->color = $request->color;
        $template->prompt = $request->prompt;
        $template->filters = __('My Templates');
        $template->premium = 0;
        $template->active = 1;
        $template->slug = Str::slug($request->title) . '-' . Str::random(6);
        $template->type = 'text';
        $template->custom_template = 1;
        $template->user_id = $userId;

        // Process input data by type
        $inputDataByType = json_decode($request->input_data_by_type, true);

        $allquestions = [];
        foreach ($inputDataByType as $inputType => $inputs) {
            foreach ($inputs as $input) {
                // Save input data as arrays
                $inputArray = [
                    'name'        => Str::slug($input['inputName']),
                    'question'    => $input['inputName'],
                    'description' => $input['inputDescription'],
                    'type'        => $inputType,
                ];
                // If input type is select, include select list values
                if ($inputType === 'select') {
                    $inputArray['selectListValues'] = $input['selectListValues'];
                }

                // Save input data array into questions array
                $allquestions[] = $inputArray;
            }
        }
        $questions = json_encode($allquestions, JSON_UNESCAPED_SLASHES);
        $template->questions = $questions;

        $template->save();

        if (OpenaiGeneratorFilter::where('name', __('My Templates'))->first() == null) {
            $newFilter = new OpenaiGeneratorFilter;
            $newFilter->name = __('My Templates');
            $newFilter->save();
        }

        $setting = Setting::getCache();
        $freeOpenAiItems = $setting->free_open_ai_items;
        $freeOpenAiItems[] = $template->slug;
        $setting->update([
            'free_open_ai_items' => $freeOpenAiItems ?: [],
        ]);
    }

    public function openAICustomAddOrUpdate($id = null)
    {
        $userId = Auth::id();
        if ($id == null) {
            $template = null;
        } else {
            $template = OpenAIGenerator::where('id', $id)->where('user_id', $userId)->firstOrFail();
        }
        $filters = OpenaiGeneratorFilter::orderBy('name', 'desc')->get();

        return view('panel.user.openai.custom.form', compact('template', 'filters'));
    }

    public function openAICustomDelete($id = null)
    {
        $userId = Auth::id();
        $template = OpenAIGenerator::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $template->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function openAIFavoritesList()
    {
        return view('panel.user.openai.list_favorites');
    }

    // docsFavorite
    public function docsFavorite(Request $request)
    {
        $exists = isFavoritedDoc($request->id);
        if ($exists) {
            $favorite = UserDocsFavorite::where('user_openai_id', $request->id)->where('user_id', Auth::id())->firstOrFail();
            $favorite->delete();
            $action = 'unfavorite';
        } else {
            $favorite = new UserDocsFavorite;
            $favorite->user_id = Auth::id();
            $favorite->user_openai_id = $request->id;
            $favorite->save();
            $action = 'favorite';
        }

        return response()->json(['action' => $action]);
    }

    public function openAIFavorite(Request $request)
    {
        $exists = isFavorited($request->id);
        if ($exists) {
            $favorite = UserFavorite::where('openai_id', $request->id)->where('user_id', Auth::id())->firstOrFail();
            $favorite->delete();
            $action = 'unfavorite';
        } else {
            $favorite = new UserFavorite;
            $favorite->user_id = Auth::id();
            $favorite->openai_id = $request->id;
            $favorite->save();
            $action = 'favorite';
        }

        return response()->json(['action' => $action]);
    }

    public function openAIGenerator(Request $request, $slug)
    {
        $openai = OpenAIGenerator::whereSlug($slug)->firstOrFail();
        if ($slug === 'ai_image_generator') {
            // FluxProQueueCheck::updateFluxProImages();
        }

        if ($slug === 'ai_video_to_video' && ! MarketplaceHelper::isRegistered('ai-video-to-video')) {
            return redirect()->route('dashboard.user.index')->with(['message' => __('Feature not available'), 'type' => 'error']);
        }

        $userOpenai = $this->openai($request, null)
            ->where('openai_id', $openai->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $elevenlabServiceVoice = [];
        $elevenlabs = null;

        if ($openai->type === 'voiceover' || $openai->type === 'isolator') {
            $elevenlabs = ElevenlabVoice::query()
                ->select('voice_id', 'name')
                ->where('status', 1)
                ->where('user_id', Auth::id())
                ->whereNotNull('voice_id')
                ->get();

            $service = new ElevenlabsService;
            if ($service->getVoices() !== []) {
                $elevenlabServiceVoice = json_decode($service->getVoices(), true);
            }
        }
        $list = OpenAIGenerator::query()
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereNull('user_id');
            })->where('active', true)->get(['id', 'title', 'slug', 'image']);

        return view(
            'panel.user.openai.generator',
            compact('openai', 'list', 'userOpenai', 'elevenlabs', 'elevenlabServiceVoice')
        );
    }

    public function openAIGeneratorWorkbook($slug)
    {
        $openai = OpenAIGenerator::whereSlug($slug)->firstOrFail();
        $settings2 = SettingTwo::getCache();
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');

        if (setting('default_ai_engine', EngineEnum::OPEN_AI->value) == EngineEnum::ANTHROPIC->value) {
            $apiUrl = base64_encode('https://api.anthropic.com/v1/messages');
        }

        if ($settings2->openai_default_stream_server == 'backend') {
            $apikeyPart1 = base64_encode(rand(1, 100));
            $apikeyPart2 = base64_encode(rand(1, 100));
            $apikeyPart3 = base64_encode(rand(1, 100));
        } else {
            // Fetch the Site Settings object with openai_api_secret
            $apiKey = ApiHelper::setOpenAiKey();

            if (setting('default_ai_engine', EngineEnum::OPEN_AI->value) == EngineEnum::ANTHROPIC->value) {
                $apiKey = ApiHelper::setAnthropicKey();
            }

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

        $apiSearchId = base64_encode($settings2->serper_api_key);

        if ($slug == 'ai_vision' || $slug == 'ai_pdf' || $slug == 'ai_chat_image' || $slug == 'ai_realtime_voice_chat') {

            $isPaid = false;
            $userId = Auth::user()->id;

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

            $category = OpenaiGeneratorChatCategory::whereSlug($slug)->firstOrFail();

            if ($isPaid == false && $category->plan == 'premium' && auth()->user()->type !== 'admin') {
                return redirect()->back()->with(['message' => __('Needs a Premium access'), 'type' => 'error']);
            }

            $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $category->id)->orderBy('is_pinned', 'desc')->orderBy('updated_at', 'desc');
            $list = $list->get();
            $chat = $list->first();
            $aiList = OpenaiGeneratorChatCategory::all();
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

            return view('panel.user.openai_chat.chat', compact(
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
                'models'
            ));
        }

        $view = 'panel.user.openai.generator_workbook';
        $models = Entity::planModels();
        $list = OpenAIGenerator::query()
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereNull('user_id');
            })->where('active', true)->get(['id', 'title', 'slug', 'image']);

        return view($view, compact(
            'list',
            'openai',
            'apiSearch',
            'apiSearchId',
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'apiUrl',
            'models'
        ));
    }

    public function openAIGeneratorWorkbookSave(Request $request)
    {
        $workbook = UserOpenai::where('slug', $request->workbook_slug)->where('user_id', auth()->user()->id)->firstOrFail();
        $workbook->output = $request->workbook_text;
        $workbook->title = $request->workbook_title;
        $workbook->save();

        return response()->json([], 200);
    }

    // Chat
    public function openAIChat()
    {
        $chat = Auth::user()->openaiChat;

        return view('panel.user.openai.chat', compact('chat'));
    }

    public static function sanitizeSVG($uploadedSVG)
    {

        $sanitizer = new Sanitizer;
        $content = file_get_contents($uploadedSVG);
        $cleanedData = $sanitizer->sanitize($content);
        $added = file_put_contents($uploadedSVG, $cleanedData);

        return $uploadedSVG;
    }

    // Profile user settings
    public function userSettings()
    {
        $user = Auth::user();

        return view('panel.user.settings.index', compact('user'));
    }

    public function userSettingsSave(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'phone'   => 'nullable|string|max:15',
            'country' => 'nullable',
            'state'   => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:255',
            'postal'  => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->country = $request->country;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->postal = $request->postal;
        $user->address = $request->address;

        if (! empty($request->old_password)) {
            $validated = $request->validateWithBag('updatePassword', [
                'old_password' => ['required', 'current_password'],
                'new_password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $user->password = Hash::make($request->new_password);
        }

        if (empty($request->old_password) && ! empty($request->new_password)) {
            $isOneOfSocialTokensNotEmpty =
                ! empty($user->google_token) || ! empty($user->github_token) || ! empty($user->facebook_token);

            if ($isOneOfSocialTokensNotEmpty) {
                $validated = $request->validateWithBag('updatePassword', [
                    'new_password' => ['required', Password::defaults(), 'confirmed'],
                ]);

                $user->password = Hash::make($request->new_password);
            }
        }

        if ($request->hasFile('avatar')) {
            $path = 'upload/images/avatar/';
            $image = $request->file('avatar');

            if ($image->getClientOriginalExtension() === 'svg') {
                $image = self::sanitizeSVG($request->file('avatar'));
            }

            $image_name = Str::random(4) . '-' . Str::slug($user?->fullName()) . '-avatar.' . $image->getClientOriginalExtension();

            // Image extension check
            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                $data = [
                    'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                ];

                return response()->json($data, 419);
            }

            $image->move($path, $image_name);

            $user->avatar = '/' . $path . $image_name;
        }

        CreateActivity::for($user, 'Updated', 'Profile Information');
        $user->save();
    }

    public function userSettingsUpdate(Request $request): RedirectResponse
    {
        $request->validate([

            'phone'   => 'nullable|string|max:15',
            'country' => 'nullable',
            'state'   => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:255',
            'postal'  => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        if (! $user = Auth::user()) {
            return redirect()->back()->with(['message' => __('Unauthorized'), 'type' => 'error']);
        }

        $user->update($request->only(['phone', 'country', 'state', 'city', 'postal', 'address']));

        CreateActivity::for($user, 'Updated', 'Profile Address Information');

        return redirect()->back()->with(['message' => __('Updated Successfully'), 'type' => 'success']);
    }

    // markDashNotifySeen
    public function markDashNotifySeen()
    {
        $user = Auth::user();
        $user->dash_notify_seen = true;
        $user->save();

        return response()->json(['status' => 'success']);
    }

    // Purchase
    public function subscriptionPlans()
    {

        // check if any payment gateway enabled
        $activeGateways = Gateways::where('is_active', 1)->get();
        if ($activeGateways->count() > 0) {
            $is_active_gateway = 1;
        } else {
            $is_active_gateway = 0;
        }

        // check if any subscription is active
        $userId = Auth::user()->id;

        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            $activesubid = $activeSub->plan_id;
        } else {
            $activeSub_yokassa = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSub_yokassa != null) {
                $activesubid = $activeSub_yokassa->plan_id;
            } else {
                $activesubid = 0;
            }
        }

        $openAiList = OpenAIGenerator::query()->get();

        $plansSubscriptionMonthly = Plan::where([['type', '=', TypeEnum::SUBSCRIPTION->value], ['frequency', '=', FrequencyEnum::MONTHLY->value], ['active', 1]])->get()->sortBy('price');
        $plansSubscriptionLifetime = Plan::where([['type', '=', TypeEnum::SUBSCRIPTION->value], ['active', 1]])
            ->where(function ($query) {
                $query->where('frequency', '=', FrequencyEnum::LIFETIME_YEARLY->value)
                    ->orWhere('frequency', '=', FrequencyEnum::LIFETIME_MONTHLY->value);
            })
            ->get()->sortBy('price');
        $plansSubscriptionAnnual = Plan::where([['type', '=', TypeEnum::SUBSCRIPTION->value], ['frequency', '=', FrequencyEnum::YEARLY->value], ['active', 1]])->get()->sortBy('price');
        $prepaidplans = Plan::where([['type', '=', TypeEnum::TOKEN_PACK->value], ['active', 1]])->get()->sortBy('price');

        $view = 'panel.user.finance.subscriptionPlans';

        return view($view, compact('plansSubscriptionMonthly', 'plansSubscriptionLifetime', 'plansSubscriptionAnnual', 'prepaidplans', 'openAiList', 'is_active_gateway', 'activeGateways', 'activesubid'));
    }

    // Invoice - Billing
    public function invoiceList()
    {
        $user = Auth::user();
        $list = $user->orders;

        return view('panel.user.orders.index', compact('list'));
    }

    public function invoiceSingle($order_id)
    {
        if (auth()->user()->isAdmin()) {
            $invoice = UserOrder::where('order_id', $order_id)->firstOrFail();
        } else {
            $invoice = UserOrder::where('order_id', $order_id)->where('user_id', auth()->user()->id)->firstOrFail();
        }

        return view('panel.user.orders.invoice', compact('invoice'));
    }

    public function ordersExport($type)
    {
        $service = new OrdersExportService;

        return match ($type) {
            'pdf'   => $service->exportAsPdf(),
            'excel' => $service->exportAsExcel(),
            'csv'   => $service->exportAsCsv(),
            default => redirect()->back()->with('error', 'Invalid export type'),
        };
    }

    public function userOrdersList($user_id)
    {
        $user = User::findOrFail($user_id);
        $list = $user->orders;

        return view('panel.user.orders.index', compact('list', 'user'));
    }

    public function documentsAll(Request $request, $folderID = null)
    {
        $DOCS_PER_PAGE = 20;

        $listOnly = $request->listOnly;
        $filter = $request->filter ?? 'all';
        $sort = $request->sort ?? 'created_at';
        $sortAscDesc = $request->sortAscDesc ?? 'desc';

        $items = $this->openai($request, $folderID)
            ->where('folder_id', $folderID)
            ->orderBy($sort, $sortAscDesc)
            ->paginate(20);

        if ($folderID !== null) {
            $currfolder = Folders::query()
                ->where(function (Builder $query) {
                    $query
                        ->where('created_by', auth()->id())
                        ->orWhere('team_id', auth()->user()->team_id);
                })
                ->findOrFail($folderID);
        } else {
            $currfolder = null;
        }

        // if(($items->total() == 0) && $folderID !== null){
        //     $items = $this->openai($request, $folderID)
        //     ->where('folder_id', null)
        //     ->orderBy('created_at', 'desc')->paginate(20);
        //     $currfolder = null;
        // }else{
        //     if ($folderID !== null) {
        //         $currfolder = Folders::query()
        //             ->where(function (Builder $query) {
        //                 $query
        //                     ->where('created_by', auth()->id())
        //                     ->orWhere('team_id', auth()->user()->team_id);
        //             })
        //             ->findOrFail($folderID);
        //     } else {
        //         $currfolder = null;
        //     }
        // }

        if ($listOnly) {
            return view('panel.user.openai.documents_container', compact('items', 'currfolder', 'filter'))->render();
        }

        return view('panel.user.openai.documents', compact('items', 'currfolder', 'filter'));
    }

    protected function openai(Request $request, $folderID = null)
    {
        $team = $request->user()->getAttribute('team');

        $myCreatedTeam = $request->user()->getAttribute('myCreatedTeam');

        return UserOpenai::query()
            ->with('generator', 'isFavoriteDocRelation')
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

    public function updateFolder(Request $request, $folder)
    {
        $request->validate([
            'newFolderName' => 'required|string|max:255',
        ]);

        $folder = Folders::findOrFail($folder);
        $folder->name = $request->input('newFolderName');
        $folder->save();

        return response()->json(['message' => __('Folder name updated successfully')]);
    }

    public function updateFile(Request $request, $slug)
    {
        $request->validate([
            'newFileName' => 'required|string|max:255',
        ]);

        $file = UserOpenai::where('slug', $slug)->where('user_id', auth()->user()->id)->firstOrFail();
        $file->generator->title = $request->input('newFileName');
        $file->generator->save();

        return response()->json(['message' => __('File name updated successfully')]);
    }

    public function deleteFolder(Request $request, $folder)
    {
        $folder = Folders::findOrFail($folder);
        $all = $request->all;
        if ($all) {
            foreach ($folder->userOpenais as $userOpenai) {
                $userOpenai->delete();
            }
        }
        $folder->delete();

        return response()->json(['message' => __('Folder deleted successfully')]);
    }

    public function newFolder(Request $request)
    {
        $request->validate([
            'newFolderName' => 'required|string|max:255',
        ]);

        $newFolder = new Folders;
        $newFolder->name = $request->newFolderName;
        $newFolder->created_by = auth()->user()->id;
        $newFolder->save();

        return back()->with(['message' => __('Added successfuly'), 'type' => 'success']);
    }

    public function moveToFolder(Request $request)
    {
        $folderID = $request->selectedFolderId;
        $fileSlug = $request->fileslug;

        $workbook = UserOpenai::where('slug', $fileSlug)->where('user_id', auth()->user()->id)->firstOrFail();
        $workbook->folder_id = $folderID;
        $workbook->save();

        return back()->with(['message' => __('Moved successfuly'), 'type' => 'success']);
    }

    public function documentsSingle($slug)
    {
        $workbook = UserOpenai::where('slug', $slug)->where('user_id', auth()->user()->id)->firstOrFail();

        $openai = $workbook->generator;

        $integrations = Auth::user()->getAttribute('integrations');

        $wordpress = UserIntegration::query()
            ->where('user_id', Auth::user()->id)->first();

        if (isset($wordpress)) {
            $wordpressExist = (bool) $wordpress->credentials['domain']['value'];
        } else {
            $wordpressExist = false;
        }

        $checkIntegration = Integration::query()->whereHas('hasExtension')->count();

        return view('panel.user.openai.documents_workbook', compact('wordpressExist', 'checkIntegration', 'workbook', 'openai', 'integrations'));
    }

    public function documentsDelete($slug)
    {
        $workbook = UserOpenai::where('slug', $slug)->where('user_id', auth()->user()->id)->firstOrFail();

        try {
            if ($workbook->storage == UserOpenai::STORAGE_LOCAL) {
                $file = str_replace('/uploads/', '', $workbook->output);
                Storage::disk('public')->delete($file);
            } elseif ($workbook->storage == UserOpenai::STORAGE_AWS) {
                $file = str_replace('/', '', parse_url($workbook->output)['path']);
                Storage::disk('s3')->delete($file);
            } else {
                // Manual deleting depends on response
                if (str_contains($workbook->output, 'https://')) {
                    // AWS Storage
                    $file = str_replace('/', '', parse_url($workbook->output)['path']);
                    Storage::disk('s3')->delete($file);
                } else {
                    $file = str_replace('/uploads/', '', $workbook->output);
                    Storage::disk('public')->delete($file);
                }
            }
            $basefilename = basename($workbook->output);
            Storage::disk('thumbs')->delete($basefilename);
        } catch (Throwable $th) {
            // throw $th;
        }

        $workbook->delete();

        return redirect()->route('dashboard.user.openai.documents.all')->with(['message' => __('Document deleted successfuly'), 'type' => 'success']);
    }

    public function documentsImageDelete($slug)
    {
        $workbook = UserOpenai::where('slug', $slug)->where('user_id', auth()->user()->id)->firstOrFail();
        if ($workbook->storage == UserOpenai::STORAGE_LOCAL) {
            $file = str_replace('/uploads/', '', $workbook->output);
            Storage::disk('public')->delete($file);
        } elseif ($workbook->storage == UserOpenai::STORAGE_AWS) {
            $file = str_replace('/', '', parse_url($workbook->output)['path']);
            Storage::disk('s3')->delete($file);
        } else {

            // Manual deleting depends on response
            if (str_contains($workbook->output, 'https://')) {
                // AWS Storage
                $file = str_replace('/', '', parse_url($workbook->output)['path']);
                Storage::disk('s3')->delete($file);
            } else {
                $file = str_replace('/uploads/', '', $workbook->output);
                Storage::disk('public')->delete($file);
            }

        }

        $basefilename = basename($workbook->output);
        $workbook->delete();
        Storage::disk('thumbs')->delete($basefilename);

        return back()->with(['message' => __('Deleted successfuly'), 'type' => 'success']);
    }

    // Affiliates
    public function affiliatesList()
    {
        abort_if(Helper::setting('feature_affilates') == 0, 404);
        $onetimeCommission = setting('onetime_commission', 0);
        $user = Auth::user();
        $list = $user?->affiliates;
        $list2 = $user?->withdrawals;
        $totalEarnings = 0;
        foreach ($list as $affUser) {
            if ($onetimeCommission) {
                // if one time commission is open then get only the first order
                $totalEarnings += $affUser->orders->sortBy('id')->first()?->affiliate_earnings;
            } else {
                $totalEarnings += $affUser->orders->sum('affiliate_earnings');
            }
        }
        $totalWithdrawal = 0;
        foreach ($list2 as $affWithdrawal) {
            $totalWithdrawal += $affWithdrawal->amount;
        }

        return view('panel.user.affiliate.index', compact('list', 'list2', 'totalEarnings', 'totalWithdrawal'));
    }

    public function affiliatesUsers(Request $request)
    {
        $setting = Setting::getCache();

        $defaultCurrency = Currency::find($setting->default_currency)->symbol;

        $query = User::where('affiliate_id', auth()->user()->id);

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        if ($request->has('startDate') && $request->input('startDate')) {
            $query->whereDate('created_at', '>=', $request->input('startDate'));
        }

        if ($request->has('endDate') && $request->input('endDate')) {
            $query->whereDate('created_at', '<=', $request->input('endDate'));
        }

        $list = $query->paginate(10);

        foreach ($list as $user) {
            $affiliate = UserAffiliate::where('user_id', $user->id)->first();
            if ($affiliate) {
                $user->affiliate_data = $affiliate;
            }
        }

        return view('panel.user.affiliate.users', compact(['list', 'defaultCurrency']));
    }

    public function affiliatesListSendInvitation(Request $request)
    {
        $user = Auth::user();

        $sendTo = $request->to_mail;

        dispatch(new SendInviteEmail($user, $sendTo));

        return response()->json([], 200);
    }

    public function affiliatesListSendRequest(Request $request)
    {
        $user = Auth::user();
        $list = $user->affiliates;
        $list2 = $user->withdrawals;

        $totalEarnings = 0;
        foreach ($list as $affOrders) {
            $totalEarnings += $affOrders->orders->sum('affiliate_earnings');
        }
        $totalWithdrawal = 0;
        foreach ($list2 as $affWithdrawal) {
            $totalWithdrawal += $affWithdrawal->amount;
        }
        if ($totalEarnings - $totalWithdrawal >= $request->amount) {
            $user->affiliate_bank_account = $request->affiliate_bank_account;
            $user->save();
            $withdrawalReq = new UserAffiliate;
            $withdrawalReq->user_id = Auth::id();
            $withdrawalReq->amount = $request->amount;
            $withdrawalReq->save();

            CreateActivity::for($user, 'Sent', 'Affiliate Withdraw Request', route('dashboard.admin.affiliates.index'));
        } else {
            return response()->json(['error' => __('ERROR')], 411);
        }
    }

    public function apiKeysList()
    {
        abort_if(
            Helper::appIsNotDemo() &&
            (
                (int) Helper::setting('user_api_option') === 0
                &&
                ! auth()->user()?->relationPlan?->getAttribute('user_api')
            ), 404);

        $user = Auth::user();
        $list = $user?->api_keys;
        $anthropic_api_keys = $user?->anthropic_api_keys;
        $gemini_api_keys = $user?->gemini_api_keys;

        return view('panel.user.apiKeys.index', compact('list', 'anthropic_api_keys', 'gemini_api_keys'));
    }

    public function apiKeysSave(Request $request)
    {
        if (Helper::appIsDemo()) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => __('This feature is disabled in Demo version.'),
                    'type'    => 'success',
                ], 200);
            }

            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $user = Auth::user();
        if ($user) {
            $user->api_keys = $request->api_keys;
            $user->anthropic_api_keys = $request->anthropic_api_keys;
            $user->gemini_api_keys = $request->gemini_api_keys;
            $user->save();
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => __('Settings saved successfully.'),
                'type'    => 'success',
            ], 200);
        }

        return redirect()->back();
    }

    public function overview()
    {
        $overviewData = [
            [
                'title' => 'Image Documents',
                'slug'  => 'image_documents',
                'count' => 0,
            ],
            [
                'title' => 'Code Documents',
                'slug'  => 'code_documents',
                'count' => 0,
            ],
            [
                'title' => 'Other Documents',
                'slug'  => 'other_documents',
                'count' => 0,
            ],
        ];
        $total = 0;

        $userId = Auth::id();
        $userOpenai = UserOpenai::where('user_id', $userId)->with('generatorWithType')->get();
        $imageCount = 0;
        $codeCount = 0;
        $otherCount = 0;
        foreach ($userOpenai as $key => $value) {
            if ($value->generator_type == 'image') {
                $imageCount++;
            } elseif ($value->generator_type == 'code') {
                $codeCount++;
            } else {
                $otherCount++;
            }
            $total++;
        }
        $overviewData[0]['count'] = $imageCount;
        $overviewData[1]['count'] = $codeCount;
        $overviewData[2]['count'] = $otherCount;

        $percantageOther = round(($otherCount / $total) * 100);

        return response()->json(['data' => $overviewData, 'total' => $total, 'percantageOther' => $percantageOther]);
    }

    public function deleteAccount()
    {
        return view('panel.user.settings.deleteAccount');
    }

    public function deleteAccountRequest(Request $request)
    {
        abort_if(Helper::appIsDemo(), 404);
        $request->validate([
            'password' => 'required',
        ]);
        $user = Auth::user();
        if (Hash::check($request->password, $user->password)) {
            $oldRequest = AccountDeletionReqs::where('user_id', $user->id)->first();
            if ($oldRequest) {
                return response()->json(['message' => __('You have already requested to delete your account')], 409);
            }
            $deletionRequest = new AccountDeletionReqs;
            $deletionRequest->user_id = $user->id;
            $deletionRequest->save();

            return response()->json(['message' => __('Your account deletion request has been successfully submitted')], 200);
        } else {
            return response()->json(['message' => __('Password is incorrect')], 401);
        }
    }

    public function exportInvoices(Request $request)
    {
        $type = 'pdf';
        $cleanDates = static function ($date) {
            return preg_replace('/(\+|\-)?(\d{2})(\d{2})$/', '+$2:$3', str_replace('GMT ', '', preg_replace('/\s*\(.*\)$/', '', $date)));
        };
        $startDate = Carbon::createFromFormat('D M d Y H:i:s O', $cleanDates($request->start_date));
        $endDate = Carbon::createFromFormat('D M d Y H:i:s O', $cleanDates($request->end_date));
        $invoices = UserOrder::whereBetween('created_at', [$startDate, $endDate])->get();
        $service = new OrdersExportService;

        return match ($type) {
            'pdf'   => $service->exportAsPdf($invoices),
            'excel' => $service->exportAsExcel(),
            'csv'   => $service->exportAsCsv(),
            default => redirect()->back()->with('error', 'Invalid export type'),
        };
    }
}
