<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\CreateActivity;
use App\Actions\EmailConfirmation;
use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Enums\Roles;
use App\Extensions\MegaMenu\System\Models\MegaMenu;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\MarketplaceHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Finance\PaymentProcessController;
use App\Models\AccountDeletionReqs;
use App\Models\Chatbot\Chatbot;
use App\Models\ChatCategory;
use App\Models\Clients;
use App\Models\Coupon;
use App\Models\CustomSettings;
use App\Models\Faq;
use App\Models\Favourite;
use App\Models\Finance\AiChatModelPlan;
use App\Models\Frontend\FrontendSectionsStatus;
use App\Models\Frontend\FrontendSetting;
use App\Models\FrontendForWho;
use App\Models\FrontendFuture;
use App\Models\FrontendGenerators;
use App\Models\FrontendTools;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\HowitWorks;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorChatCategory;
use App\Models\OpenaiGeneratorFilter;
use App\Models\Plan;
use App\Models\Section\AdvancedFeaturesSection;
use App\Models\Section\BannerBottomText;
use App\Models\Section\ComparisonSectionItems;
use App\Models\Section\FeaturesMarquee;
use App\Models\Section\FooterItem;
use App\Models\Setting;
use App\Models\SocialMediaAccounts;
use App\Models\Testimonials;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserAffiliate;
use App\Models\UserOpenaiChat;
use App\Services\Assistant\AssistantService;
use App\Services\CountryCodeService;
use App\Services\Dashboard\DashboardService;
use App\Services\UsersExportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;
use JsonException;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct(
        public DashboardService $service,
        public ExtensionRepositoryInterface $extensionRepository
    ) {
        $this->middleware(\Fahlisaputra\Minify\Middleware\MinifyHtml::class);
    }

    /**
     * @throws JsonException
     */
    public function index(): View
    {
        $this->service->setCache();

        if (Cache::has('vip_membership')) {
            $vip_membership = Helper::showIntercomForVipMembership();
        } else {
            $vip_membership = Cache::get('vip_membership') ?: false;
        }

        return view('panel.admin.index', [
            'activity'       => $this->service->activity(),
            'latestOrders'   => $this->service->latestOrders(),
            'gatewayError'   => false,
            'vip_membership' => (bool) $vip_membership,
        ]);
    }

    private function isMobileDevice($userAgent): bool
    {
        $mobileDevices = [
            'Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi',
        ];

        foreach ($mobileDevices as $device) {
            if (str_contains($userAgent, $device)) {
                return true;
            }
        }

        return false;
    }

    // USER MANAGEMENT
    public function users(Request $request): View
    {
        $search = $request->input('search');
        $users = User::query()
            ->where('name', 'like', "%$search%")
            ->orWhere('surname', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('phone', 'like', "%$search%")
            ->paginate(25);

        return view('panel.admin.users.index', compact('users'));
    }

    public function usersSearch(Request $request): View
    {
        $search = $request->input('search');
        $users = User::query()
            ->where('name', 'like', "%$search%")
            ->orWhere('surname', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('phone', 'like', "%$search%")
            ->paginate(25);

        return view('panel.admin.users.components.users-table', compact('users'));
    }

    public function usersActivity(): View
    {
        $users = UserActivity::orderBy('created_at', 'desc')->paginate(25);

        return view('panel.admin.users.activity', compact('users'));
    }

    public function userExport($type)
    {
        $users = User::all();
        $service = new UsersExportService;
        switch ($type) {
            case 'pdf':
                return $service->exportAsPdf($users);
            case 'excel':
                return $service->exportAsExcel($users);
            case 'csv':
                return $service->exportAsCsv($users);
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }

    public function usersDashboard(): View
    {
        $totalUser = User::count();
        $oneWeekAgo = Carbon::now()->subWeek();
        $newUsersLastWeek = User::where('created_at', '>=', $oneWeekAgo)->count();
        $newUsersPercentage = $totalUser > 0 ? ($newUsersLastWeek / $totalUser) * 100 : 0;
        $today = Carbon::today();
        $todayVisitor = UserActivity::whereDate('created_at', $today)->count();
        $onlineUsers = 0;
        $users = User::all();

        foreach ($users as $user) {
            if (Cache::has('user-is-online-' . $user->id)) {
                $onlineUsers++;
            }
        }
        $countryData = User::select(DB::raw('country, count(*) as count'))
            ->groupBy('country')
            ->get()
            ->map(function ($country) {
                return [
                    'code'  => CountryCodeService::getCountryCode($country->country),
                    'name'  => $country->country,
                    'value' => $country->count,
                ];
            })
            ->filter(function ($country) {
                return $country['code'] !== null;
            })
            ->toArray();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $monthlyData = User::select(
            DB::raw('DAY(created_at) as day'),
            DB::raw('COUNT(*) as count')
        )->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('DAY(created_at)'))
            ->orderBy('day')
            ->get();

        $yearlyData = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        $daysInMonth = Carbon::now()->daysInMonth;
        $data = array_fill(1, $daysInMonth, 0);
        $monthlyUserCounts = array_fill(1, 12, 0);
        foreach ($yearlyData as $record) {
            $monthlyUserCounts[$record->month] = $record->count;
        }
        foreach ($monthlyData as $record) {
            $data[$record->day] = $record->count;
        }
        $totalYearCount = User::whereYear('created_at', $currentYear)->count();

        return view('panel.admin.users.dashboard', compact(['totalUser', 'newUsersPercentage', 'todayVisitor', 'onlineUsers', 'countryData', 'data', 'totalYearCount', 'monthlyUserCounts']));
    }

    public function freeFeature(Request $request): View
    {
        $openAiList = OpenAIGenerator::query()->get();
        $selectedAiList = Helper::setting('free_open_ai_items') ?? [];
        $groupedAiList = $openAiList->groupBy('filters');
        $checkedGroups = [];
        foreach ($groupedAiList as $key => $items) {
            $hasCheckedItems = $items->contains(function ($item) use ($selectedAiList) {
                return in_array($item->slug, $selectedAiList, true);
            });

            if ($hasCheckedItems) {
                $checkedGroups[$key] = true;
            }
        }

        return view('panel.admin.finance.free-feature', compact('checkedGroups', 'openAiList', 'selectedAiList'));
    }

    public function freeFeatureSave(Request $request): RedirectResponse
    {
        $setting = Setting::getCache();
        $setting->free_open_ai_items = $request->openaiItems ?: [];
        if ($setting->save()) {
            return back()->with(['message' => __('Save free feature'), 'type' => 'success']);
        }

        return back()->with(['message' => __('Save free feature'), 'type' => 'error']);
    }

    public function usersEdit(User $user): View
    {
        return view('panel.admin.users.edit', compact('user'));
    }

    public function usersFinance($id): View
    {
        $user = User::whereId($id)->firstOrFail();
        $sub = getCurrentActiveSubscription($user->id) ?? getCurrentActiveSubscriptionYokkasa($user->id);
        $plan = Plan::where('id', $sub?->plan_id)->first();

        return view('panel.admin.users.finance', compact('user', 'sub', 'plan'));
    }

    public function usersAdd(): View
    {
        return view('panel.admin.users.create');
    }

    public function usersStore(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $validator = Validator::make($request->all(), [
            'name'                     => 'required|string|max:255',
            'surname'                  => 'required|string|max:255',
            'email'                    => 'required|email|max:255|unique:users',
            'password'                 => 'required|min:8',
            'repassword'               => 'required|same:password',
            'phone'                    => 'nullable|string|max:15',
            'avatar'                   => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type'                     => ['required', new Enum(Roles::class)],
            'country'                  => 'nullable',
            'status'                   => 'nullable|in:0,1',
            'entities.*.*.isUnlimited' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    // this function used because the request sents from js
                    if (! in_array($value, [true, false, 'true', 'false', 1, 0, '1', '0', 'on', 'off'], true)) {
                        $fail("The $attribute field must be true or false.");
                    }
                },
            ],
            'entities.*.*.credit' => 'sometimes|numeric',
        ], [
            'repassword.same' => __('The password and re-password must match.'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $entities = $request->input('entities');
        $user = User::query()->create([
            'name'                    => $request->name,
            'surname'                 => $request->surname,
            'email'                   => $request->email,
            'phone'                   => $request->phone,
            'country'                 => $request->country,
            'type'                    => $request->type,
            'status'                  => $request->status,
            'email_confirmation_code' => Str::random(67),
            'password'                => Hash::make($request->password),
            'email_verification_code' => Str::random(67),
            'affiliate_code'          => Str::upper(Str::random(12)),
        ]);

        $user->updateCredits($entities);

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
                return back()->with(['message' => __('The file extension must be jpg, jpeg, png, webp or svg.'), 'type' => 'error']);
            }
            $image->move($path, $image_name);
            $user->avatar = $path . $image_name;
            $user->save();
        }

        EmailConfirmation::forUser($user)->send();

        return back()->with(['message' => __('Created Successfully'), 'type' => 'success']);
    }

    public function usersDelete($id): RedirectResponse
    {
        $user = User::query()->findOrFail($id);
        if (
            $user->isAdmin() &&
            User::query()->where('type', Roles::SUPER_ADMIN->value)->orWhere('type', Roles::ADMIN->value)->count() <= 1
        ) {
            $data = [
                'errors' => ['There must be at least one admin.'],
            ];

            return back()->with($data);
        }
        $generators = OpenaiGeneratorChatCategory::query()
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            }, function ($query) {
                return $query->whereRaw('1 = 0');
            })
            ->get();
        foreach ($generators as $generator) {
            $chats = UserOpenaiChat::where('user_id', $user->id)->where('openai_chat_category_id', $generator->id)->get();
            if ($chats) {
                foreach ($chats as $chat) {
                    $chat->delete();
                }
            }
            $generator->delete();
        }
        $user->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function usersSave(Request $request): void
    {
        $request->validate([
            'user_id'                  => 'required|exists:users,id',
            'name'                     => 'required|string|max:255',
            'surname'                  => 'required|string|max:255',
            'phone'                    => 'nullable|string|max:15',
            'email'                    => 'required|email|max:255|unique:users,email,' . $request->user_id,
            'country'                  => 'nullable',
            'type'                     => ['required', new Enum(Roles::class)],
            'status'                   => 'nullable|in:0,1',
            'entities.*.*.isUnlimited' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    // this function used because the request sents from js
                    if (! in_array($value, [true, false, 'true', 'false', 1, 0, '1', '0'], true)) {
                        $fail("The $attribute field must be true or false.");
                    }
                },
            ],
            'entities.*.*.credit' => 'sometimes|numeric',
        ]);
        $entities = $request->input('entities');
        $user = User::query()->find($request->user_id);
        $user->update([
            'name'    => $request->name,
            'surname' => $request->surname,
            'phone'   => $request->phone,
            'email'   => $request->email,
            'country' => $request->country,
            'type'    => $request->type,
            'status'  => $request->status,
        ]);

        $user->updateCredits($entities);
    }

    // OPENAI MANAGEMENT
    public function openAIList()
    {
        $list = OpenAIGenerator::where('custom_template', '!=', 1)
            ->orderByRaw("CASE WHEN slug LIKE 'ai\_%' THEN 0 ELSE 1 END, title ASC")
            ->get();

        return view('panel.admin.openai.list', compact('list'));
    }

    public function openAIListUpdateStatus(Request $request)
    {
        $status = $request->status;
        $openai = OpenAIGenerator::whereId($request->entry_id)->first();

        if ($openai) {
            if ($status == 1 or $status == 0) {
                $openai->active = $status;
                $openai->save();

                $this->toggleUpdateSettings($openai, $status);
            } else {
                return response()->json([], 403);
            }
        } else {
            return response()->json([], 403);
        }
    }

    public function toggleUpdateSettings(OpenAIGenerator $openAIGenerator, $status)
    {
        $data = [
            'ai_article_wizard_generator' => 'feature_ai_article_wizard',
            'ai_writer'                   => 'feature_ai_writer',
            'ai_rewriter'                 => 'feature_ai_rewriter',
            'ai_chat_image'               => 'feature_ai_chat_image',
            'ai_image_generator'          => 'feature_ai_image',
            'ai_code_generator'           => 'feature_ai_code',
            'ai_speech_to_text'           => 'feature_ai_speech_to_text',
            'ai_voiceover'                => 'feature_ai_voiceover',
            'ai_vision'                   => 'feature_ai_vision',
            'ai_pdf'                      => 'feature_ai_pdf',
            'ai_youtube'                  => 'feature_ai_youtube',
            'ai_rss'                      => 'feature_ai_youtube',
        ];

        if (array_key_exists($openAIGenerator->getAttribute('slug'), $data)) {
            if ($setting = Setting::query()->first()) {
                $setting->update([
                    $data[$openAIGenerator->getAttribute('slug')] => $status,
                ]);
            }
        }
    }

    public function openAIListUpdatePackageStatus(Request $request)
    {
        $status = $request->status;
        $openai = OpenAIGenerator::whereId($request->entry_id)->first();
        if ($status == 1 or $status == 0) {
            $openai->premium = $status;
            $openai->save();
        } else {
            return response()->json([], 403);
        }

    }

    public function categoryList(Request $request)
    {
        $list = ChatCategory::with('user')
            ->orderBy('name', 'asc')
            ->get();

        return view('panel.admin.openai.chat.category', compact('list'));
    }

    public function addOrUpdateCategory($id = null)
    {
        if ($id == null) {
            $item = null;
        } else {
            $item = ChatCategory::where('id', $id)->firstOrFail();
        }

        return view('panel.admin.openai.chat.category.template', compact('item'));
    }

    public function chatCategoriesAddOrUpdateSave(Request $request)
    {
        if ($request->item_id != 'undefined') {
            $item = ChatCategory::where('id', $request->item_id)->firstOrFail();
        } else {
            $item = new ChatCategory;
        }
        $item->user_id = auth()->user()->id;
        $item->name = $request->name;
        $item->save();
    }

    public function chatCategoriesDelete($id = null)
    {
        $item = ChatCategory::where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    // OPENAI CHAT CUSTOM TEMPLATES
    public function openAIChatList()
    {
        // $list = OpenaiGeneratorChatCategory::orderBy('name', 'asc')->get();
        $list = OpenaiGeneratorChatCategory::with('user')
            ->where('slug', '<>', 'ai_vision')
            ->where('slug', '<>', 'ai_pdf')
            ->orderBy('name', 'asc')
            ->get();

        return view('panel.admin.openai.chat.list', compact('list'));
    }

    public function openAIChatAddOrUpdate($id = null)
    {
        if ($id == null) {
            $template = null;
        } else {
            $template = OpenaiGeneratorChatCategory::where('id', $id)->firstOrFail();
        }

        $assistantService = new AssistantService;
        $assistants = $assistantService->listAssistant()['data'] ?? [];

        $categoryList = ChatCategory::all();

        $chatbots = Chatbot::query()->get();

        return view('panel.admin.openai.chat.form', compact('template', 'categoryList', 'chatbots', 'assistants'));
    }

    public function updatePlan(Request $request)
    {
        $status = $request->status;
        $chat = OpenaiGeneratorChatCategory::whereId($request->entry_id)->first();
        $chat->plan = $status;
        $chat->save();
    }

    public function updateChatFav(Request $req)
    {
        $id = $req->id;

        $favourites = Favourite::where('type', 'chat')
            ->where('item_id', $id)
            ->where('user_id', auth()->user()->id)
            ->get();

        if ($favourites->count() != 0) {
            $favourites->each->delete();
        } else {
            $favourite = Favourite::create([
                'user_id' => auth()->user()->id,
                'type'    => 'chat',
                'item_id' => $id,
            ]);
        }

        $favourites = Favourite::where('type', 'chat')
            ->where('user_id', auth()->user()->id)
            ->get();

        return response()->json($favourites);
    }

    public function openAIChatDelete($id = null)
    {
        $template = OpenaiGeneratorChatCategory::where('id', $id)->firstOrFail();
        $template->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function openAIChatAddOrUpdateSave(Request $request)
    {
        if ($request->template_id !== null) {
            $template = OpenaiGeneratorChatCategory::where('id', $request->template_id)->firstOrFail();
            $slug = $template->slug;
        } else {
            $template = new OpenaiGeneratorChatCategory;
            $slug = Str::slug($request->name) . '-' . Str::random(5);
        }

        if ($request->chatbot_id == 'undefined') {
            $request->chatbot_id = null;
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
        $template->category = $request->chat_category ?? '';
        $template->slug = $slug;
        $template->short_name = $request->short_name;
        $template->description = $request->description;
        $template->instructions = $request->instructions;
        $template->first_message = $request->first_message;
        $template->role = $request->role;
        $template->human_name = $request->human_name;
        $template->helps_with = $request->helps_with;
        $template->color = $request->color;
        $template->plan = 'regular';
        $template->chat_completions = $request->chat_completions;
        $template->prompt_prefix = 'As a ' . $request->role . ', ';
        $template->chatbot_id = $request->chatbot_id;
        $template->assistant = $request->assistant;
        $template->save();

        return redirect()->route('dashboard.admin.openai.chat.list')->with(['message' => __('Saved Successfully'), 'type' => 'success']);
    }

    // OPENAI CUSTOM TEMPLATES
    public function openAICustomList()
    {
        $list = OpenAIGenerator::query()
            ->with('user')
            ->orderBy('title', 'asc')
            ->where('custom_template', 1)
            ->get();

        return view('panel.admin.openai.custom.list', compact('list'));
    }

    public function openAICustomAddOrUpdate($id = null)
    {
        if ($id == null) {
            $template = null;
        } else {
            $template = OpenAIGenerator::where('id', $id)->firstOrFail();
        }
        // dd($template);
        $filters = OpenaiGeneratorFilter::orderBy('name', 'desc')->get();

        return view('panel.admin.openai.custom.form', compact('template', 'filters'));
    }

    public function openAICustomDelete($id = null)
    {
        $template = OpenAIGenerator::where('id', $id)->firstOrFail();
        $template->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    // public function openAICustomAddOrUpdateSave(Request $request)
    // {

    //     if ($request->template_id != 'undefined') {
    //         $template = OpenAIGenerator::where('id', $request->template_id)->firstOrFail();
    //     } else {
    //         $template = new OpenAIGenerator();
    //     }

    //     $template->title = $request->title;
    //     $template->description = $request->description;
    //     $template->image = $request->image;
    //     $template->color = $request->color;
    //     $template->prompt = $request->prompt;

    //     $inputNames = explode(',', $request->input_name);
    //     $inputDescriptions = explode(',', $request->input_description);
    //     $inputTypes = explode(',', $request->input_type);

    //     $i = 0;
    //     $array = [];
    //     foreach ($inputNames as $inputName) {
    //         $array[$i]['name'] = Str::slug($inputName);
    //         $array[$i]['type'] = $inputTypes[$i];
    //         $array[$i]['question'] = $inputName;
    //         $array[$i]['description'] = $inputDescriptions[$i];
    //         $i++;
    //     }

    //     $questions = json_encode($array, JSON_UNESCAPED_SLASHES);
    //     $template->active = 1;
    //     $template->slug = Str::slug($request->title).'-'.Str::random(6);
    //     $template->questions = $questions;
    //     $template->type = 'text';
    //     $template->custom_template = 1;
    //     $template->filters = $request->filters;
    //     foreach (explode(',', $request->filters) as $filter) {
    //         if (OpenaiGeneratorFilter::where('name', $filter)->first() == null) {
    //             $newFilter = new OpenaiGeneratorFilter();
    //             $newFilter->name = $filter;
    //             $newFilter->save();
    //         }
    //     }
    //     $template->premium = $request->premium;

    //     $template->save();
    // }

    public function openAICustomAddOrUpdateSave(Request $request)
    {
        if ($request->template_id != 'undefined') {
            $template = OpenAIGenerator::where('id', $request->template_id)->firstOrFail();
        } else {
            $template = new OpenAIGenerator;
        }

        // Set basic template attributes
        $template->title = $request->title;
        $template->description = $request->description;
        $template->image = $request->image;
        $template->color = $request->color;
        $template->prompt = $request->prompt;
        $template->filters = $request->filters;
        $template->premium = $request->premium;
        $template->active = 1;
        $template->slug = Str::slug($request->title) . '-' . Str::random(6);
        $template->type = 'text';
        $template->custom_template = 1;

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

        // Save template
        $template->save();

        // Save new filters
        foreach (explode(',', $request->filters) as $filter) {
            if (OpenaiGeneratorFilter::where('name', $filter)->first() == null) {
                $newFilter = new OpenaiGeneratorFilter;
                $newFilter->name = $filter;
                $newFilter->save();
            }
        }
    }

    // Openai Categories
    public function openAICategoriesList()
    {
        $list = OpenaiGeneratorFilter::query()
            ->with('user')
            ->orderBy('name', 'asc')->get();

        return view('panel.admin.openai.categories.list', compact('list'));
    }

    public function openAICategoriesAddOrUpdate($id = null)
    {
        if ($id == null) {
            $item = null;
        } else {
            $item = OpenaiGeneratorFilter::where('id', $id)->firstOrFail();
        }

        return view('panel.admin.openai.categories.form', compact('item'));
    }

    public function openAICategoriesDelete($id = null)
    {
        $item = OpenaiGeneratorFilter::where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function openAICategoriesAddOrUpdateSave(Request $request)
    {

        if ($request->item_id != 'undefined') {
            $item = OpenaiGeneratorFilter::where('id', $request->item_id)->firstOrFail();
        } else {
            $item = new OpenaiGeneratorFilter;
        }
        $item->name = $request->name;
        $item->save();
    }

    // FINANCE

    // Payment

    public function paymentPlans()
    {

        $gatewayError = false;
        // $gateway = Gateways::where("mode", "sandbox")->first();
        // if($gateway != null){
        //     if(env('APP_ENV') != 'development'){
        //         Log::info('Gateway is set to use sandbox. Please set mode to development!');
        //         $gatewayError = true;
        //     }
        // }
        $setting = Setting::getCache();
        $plans = Plan::all();

        return view('panel.admin.finance.plans.index', compact('plans', 'gatewayError', 'setting'));
    }

    public function paymentPlansSubscriptionNewOrEdit($id = null)
    {

        $activeGateways = Gateways::where('is_active', 1)->get();
        if ($activeGateways->count() > 0) {
            $isActiveGateway = 1;
        } else {
            $isActiveGateway = 0;
        }

        $generatedData = null;
        if ($id != null) {
            $generatedData = GatewayProducts::where('plan_id', $id)->get();
        }

        $openAiList = OpenAIGenerator::query()->get();

        $models = \App\Domains\Entity\Models\Entity::query()
            ->where('is_selected', 1)
            ->whereHas('tokens', function ($query) {
                $query->where('type', 'word');
            })
            ->get();

        $selectedModels = AiChatModelPlan::query()
            ->where('plan_id', $id)
            ->pluck('entity_id')
            ->toArray();

        if ($id == null) {
            $selectedAiList = [];

            return view('panel.admin.finance.plans.SubscriptionNewOrEdit', compact('isActiveGateway', 'openAiList', 'selectedAiList', 'models', 'selectedModels'));
        } else {
            $subscription = Plan::where('id', $id)->firstOrFail();

            $selectedAiList = $subscription->open_ai_items ?: [];

            $openAiList = OpenAIGenerator::all();

            $groupedAiList = $openAiList->groupBy('filters');

            $checkedGroups = [];
            foreach ($groupedAiList as $key => $items) {
                $hasCheckedItems = $items->contains(static function ($item) use ($selectedAiList) {
                    return in_array($item->slug, $selectedAiList, true);
                });

                if ($hasCheckedItems) {
                    $checkedGroups[$key] = true;
                }
            }

            return view('panel.admin.finance.plans.SubscriptionNewOrEdit', compact('checkedGroups', 'subscription', 'selectedAiList', 'isActiveGateway', 'generatedData', 'openAiList', 'models', 'selectedModels'));
        }
    }

    public function paymentPlansDelete($id)
    {
        return PaymentProcessController::deletePaymentPlan($id);
    }

    public function paymentPlansPrepaidNewOrEdit($id = null)
    {

        $activeGateways = Gateways::where('is_active', 1)->get();

        if ($activeGateways->count() > 0) {
            $isActiveGateway = 1;
        } else {
            $isActiveGateway = 0;
        }

        $generatedData = null;

        if ($id != null) {
            $generatedData = GatewayProducts::where('plan_id', $id)->get();
        }

        $openAiList = OpenAIGenerator::query()->get();

        $models = \App\Domains\Entity\Models\Entity::query()
            ->where('is_selected', 1)
            ->whereHas('tokens', function ($query) {
                $query->where('type', 'word');
            })
            ->get();

        $selectedModels = AiChatModelPlan::query()
            ->where('plan_id', $id)
            ->pluck('entity_id')
            ->toArray();

        if ($id == null) {
            $selectedAiList = [];

            return view('panel.admin.finance.plans.PrepaidNewOrEdit', compact('openAiList', 'selectedAiList', 'isActiveGateway', 'models', 'selectedModels'));
        } else {
            $subscription = Plan::where('id', $id)->first();
            $selectedAiList = $subscription->open_ai_items ?: [];

            $openAiList = OpenAIGenerator::all();

            $groupedAiList = $openAiList->groupBy('filters');
            $checkedGroups = [];
            foreach ($groupedAiList as $key => $items) {
                $hasCheckedItems = $items->contains(function ($item) use ($selectedAiList) {
                    return in_array($item->slug, $selectedAiList);
                });

                if ($hasCheckedItems) {
                    $checkedGroups[$key] = true;
                }
            }

            return view('panel.admin.finance.plans.PrepaidNewOrEdit', compact('checkedGroups', 'openAiList', 'selectedAiList', 'subscription', 'isActiveGateway', 'generatedData', 'models', 'selectedModels'));
        }
    }

    public function paymentPlansSave(Request $request)
    {
        $requireUpdate = false;
        $newPlan = false;
        if ($request->plan_id != 'undefined') {
            $plan = Plan::where('id', $request->plan_id)->firstOrFail();
        } else {
            $plan = new Plan;
            $newPlan = true;
        }

        if ($request->type == 'subscription') {
            if ($plan->price != (float) $request->price || $plan->trial_days != $request->trial_days || $plan->frequency != $request->frequency) {
                $requireUpdate = true;
            }

            $plan->active = 1;
            $plan->name = $request->name;
            $plan->description = $request->description;
            $plan->price = (float) $request->price;
            $plan->frequency = $request->frequency;
            $plan->is_featured = (int) $request->is_featured;
            $plan->stripe_product_id = $request->stripe_product_id;
            $plan->ai_name = $request->ai_name;
            // $plan->max_tokens = (int)$request->max_tokens;
            $plan->can_create_ai_images = (int) $request->can_create_ai_images;
            $plan->plan_type = $request->plan_type;
            $plan->features = $request->features;
            $plan->trial_days = $request->trial_days;
            $plan->type = $request->type;
            $plan->is_team_plan = (bool) $request->is_team_plan;
            $plan->plan_allow_seat = (int) $request->plan_allow_seat;
            $plan->open_ai_items = $request->openaiItems;
            $plan->currency = currency()->code ?: 'USD';
            $plan->save();
        } else {
            if ($plan->price != (float) $request->price) {
                $requireUpdate = true;
            }

            $plan->active = 1;
            $plan->name = $request->name;
            $plan->description = $request->description;
            $plan->price = (float) $request->price;
            $plan->is_featured = (int) $request->is_featured;
            $plan->features = $request->features;
            $plan->type = $request->type;
            $plan->is_team_plan = (bool) $request->is_team_plan;
            $plan->plan_allow_seat = (int) $request->plan_allow_seat;
            $plan->open_ai_items = $request->openaiItems;
            $plan->currency = currency()->code ?: 'USD';
            $plan->save();
        }

        //        $this->aiChatModelSave($plan->id, $request);

        try {

            if ($newPlan || $requireUpdate) {
                $tmp = PaymentProcessController::saveGatewayProducts($plan);
            }
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
            Log::info("AdminController->paymentPlansSave()->PaymentProcessController::saveGatewayProducts()\n" . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    //    private function aiChatModelSave($planId, $request)
    //    {
    //        $models = $request->get('aiChatModelItems');
    //        AiChatModelPlan::query()
    //            ->where('plan_id', $planId)
    //            ->delete();
    //        if ($models && is_array($models)) {
    //            foreach ($models as $model) {
    //                AiChatModelPlan::query()
    //                    ->create([
    //                        'plan_id' => $planId,
    //                        'entity_id' => $model,
    //                    ]);
    //            }
    //        }
    //    }

    // Testimonials

    /**
     * Index page of Testimonials in Admin
     */
    public function testimonials()
    {
        $testimonialList = Testimonials::all();

        return view('panel.admin.testimonials.index', compact('testimonialList'));
    }

    public function testimonialsNewOrEdit($id = null)
    {
        if ($id == null) {
            return view('panel.admin.testimonials.TestimonialNewOrEdit');
        } else {
            $testimonial = Testimonials::where('id', $id)->first();

            return view('panel.admin.testimonials.TestimonialNewOrEdit', compact('testimonial'));
        }
    }

    public function testimonialsDelete($id)
    {
        $testimonial = Testimonials::where('id', $id)->first();
        $testimonial->delete();

        return back()->with(['message' => __('Testimonial is deleted.'), 'type' => 'success']);
    }

    public function testimonialsSave(Request $request)
    {
        if ($request->testimonial_id != 'undefined') {
            $testimonial = Testimonials::where('id', $request->testimonial_id)->firstOrFail();
        } else {
            $testimonial = new Testimonials;
        }

        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('testimonialAvatar'), $filename);
            $testimonial->avatar = $filename;
        }

        $testimonial->full_name = $request->full_name;
        $testimonial->job_title = $request->job_title;
        $testimonial->words = $request->words;
        $testimonial->save();
    }

    // Testimonials End

    // How it Works
    public function howitWorksDefaults()
    {
        $values = json_decode('{"option": TRUE, "html": ""}');
        $default_html = 'Want to see? <a class="text-[#FCA7FF]" href="https://codecanyon.net/item/magicai-openai-content-text-image-chat-code-generator-as-saas/45408109" target="_blank">' . __('Join') . ' Magic</a>';

        // Check display bottom line
        $bottomline = CustomSettings::where('key', 'howitworks_bottomline')->first();
        if ($bottomline != null) {
            $values['option'] = $bottomline->value_int ?? 1;
            $values['html'] = $bottomline->value_html ?? $default_html;
        } else {
            $bottomline = new CustomSettings;
            $bottomline->key = 'howitworks_bottomline';
            $bottomline->title = 'Used in How it Works section bottom line. Controls visibility and HTML value of line.';
            $bottomline->value_int = 1;
            $bottomline->value_html = $default_html;
            $bottomline->save();
            $values['option'] = 1;
            $values['html'] = $default_html;
        }

        return $values;
    }

    /**
     * Index page of "How it Works" in Admin
     */
    public function howitWorks()
    {
        $howitWorksList = HowitWorks::all();
        $defaults = self::howitWorksDefaults();

        return view('panel.admin.howitworks.index', compact('howitWorksList', 'defaults'));
    }

    public function howitWorksNewOrEdit($id = null)
    {
        if ($id == null) {
            return view('panel.admin.howitworks.HowitWorksNewOrEdit');
        } else {
            $howitWorks = HowitWorks::where('id', $id)->first();

            return view('panel.admin.howitworks.HowitWorksNewOrEdit', compact('howitWorks'));
        }
    }

    public function howitWorksDelete($id)
    {
        $howitWorks = HowitWorks::where('id', $id)->first();
        $howitWorks->delete();

        return back()->with(['message' => __('How it Works Step is deleted.'), 'type' => 'success']);
    }

    public function howitWorksSave(Request $request)
    {
        if ($request->howitWorks_id != 'undefined') {
            $howitWorks = HowitWorks::where('id', $request->howitWorks_id)->firstOrFail();
        } else {
            $howitWorks = new HowitWorks;
        }
        $howitWorks->bg_color = $request->bg_color;
        if ($request->file('bg_image')) {
            $file = $request->file('bg_image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('howitWorks'), $filename);
            $howitWorks->bg_image = $filename;
        }
        $howitWorks->text_color = $request->text_color;
        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('howitWorks'), $filename);
            $howitWorks->image = $filename;
        }
        $howitWorks->order = (int) $request->order;
        $howitWorks->title = $request->title;
        $howitWorks->description = $request->description;
        $howitWorks->save();
    }

    public function howitWorksBottomLineSave(Request $request)
    {

        $bottomline = CustomSettings::where('key', 'howitworks_bottomline')->first();
        if ($bottomline != null) {

            $save = 0;
            if ($request->option != 'undefined' && $request->option != null) {
                $bottomline->value_int = $request->option == 1 ? 1 : 0;
                $save = 1;
            }

            if ($request->text != 'undefined' && $request->text != null) {
                $default_html = 'Want to see? <a class="text-[#FCA7FF]" href="https://codecanyon.net/item/magicai-openai-content-text-image-chat-code-generator-as-saas/45408109" target="_blank">' . __('Join') . ' Magic</a>';
                $bottomline->value_html = $request->text ?? $default_html;
                $save = 1;
            }

            if ($save == 1) {
                $bottomline->save();
            }

        }
    }

    // "How it Works" End

    // Clients => Bottom section of Testimonials

    /**
     * Index page of Clients in Admin
     */
    public function clients()
    {
        $clientList = Clients::all();

        return view('panel.admin.clients.index', compact('clientList'));
    }

    public function clientsNewOrEdit($id = null)
    {
        if ($id == null) {
            return view('panel.admin.clients.ClientNewOrEdit');
        } else {
            $client = Clients::where('id', $id)->first();

            return view('panel.admin.clients.ClientNewOrEdit', compact('client'));
        }
    }

    public function clientsDelete($id)
    {
        $client = Clients::where('id', $id)->first();
        $client->delete();

        return back()->with(['message' => __('Client deleted.'), 'type' => 'success']);
    }

    public function clientsSave(Request $request)
    {
        if ($request->client_id != 'undefined') {
            $client = Clients::where('id', $request->client_id)->firstOrFail();
        } else {
            $client = new Clients;
        }

        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('clientAvatar'), $filename);
            $client->avatar = $filename;
        }

        $client->alt = $request->alt;
        $client->title = $request->title;
        $client->save();
    }

    // Testimonials End

    // Affiliates
    public function affiliatesList()
    {

        $list = UserAffiliate::where('status', 'Waiting')->get();
        $list2 = UserAffiliate::whereNot('status', 'Waiting')->get();

        return view('panel.admin.affiliate.index', compact('list', 'list2'));
    }

    public function affiliatesListSent($id)
    {
        $item = UserAffiliate::whereId($id)->firstOrFail();
        $item->status = 'Sent Succesfully';
        $item->save();

        return back();
    }

    // Coupons
    public function couponsList()
    {
        $list = Coupon::get();

        return view('panel.admin.coupons.index', compact('list'));
    }

    public function couponsListUsed($id)
    {
        $coupon = Coupon::find($id);

        return view('panel.admin.coupons.used', compact('coupon'));
    }

    public function couponsDelete($id)
    {
        $coup = Coupon::find($id);
        if ($coup) {
            $coup->delete();

            return back()->with('success', 'Coupon deleted successfully');
        }

        return back()->with('error', 'Something went wrong!');
    }

    public function couponsAdd(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'discount'  => 'required|numeric|min:0|max:100',
            'limit'     => 'required|integer|min:-1',
            'code'      => 'required|in:auto,manual',
            'codeInput' => 'required_if:code,manual|max:20',
        ]);

        $newCoupon = new Coupon;
        $newCoupon->name = $request->input('name');
        $newCoupon->discount = $request->input('discount');
        $newCoupon->limit = $request->input('limit');
        $newCoupon->created_by = auth()->user()->id;

        // Check if the "code" field is set to "manual" and set the "codeInput" attribute accordingly.
        if ($request->input('code') === 'manual') {
            if (Coupon::where('code', $request->input('codeInput'))->exists()) {
                $newCoupon->code = $this->generateUniqueCode();
            } else {
                $newCoupon->code = $request->input('codeInput');
            }
        } else {
            $newCoupon->code = $this->generateUniqueCode();
        }

        $newCoupon->save();

        return redirect()->back()->with('success', 'Coupon created successfully.');
    }

    public function couponsEdit(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'discount' => 'required|numeric|min:0|max:100',
            'limit'    => 'required|integer|min:-1',
        ]);

        $newCoupon = Coupon::find($id);
        $newCoupon->name = $request->input('name');
        $newCoupon->discount = $request->input('discount');
        $newCoupon->limit = $request->input('limit');

        $newCoupon->save();

        return redirect()->back()->with('success', 'Coupon created successfully.');
    }

    public function couponsValidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid'  => false,
                'errors' => $validator->errors(),
            ]);
        }

        $isValidCoupon = $this->checkCouponValidity($request->input('code'));

        return response()->json(['valid' => $isValidCoupon]);
    }

    private function checkCouponValidity($code)
    {
        $exist = Coupon::where('code', $code)->first();
        if ($exist && (! ($exist->usersUsed->count() >= $exist->limit) || $exist->limit == -1)) {
            return true;
        }

        return false;
    }

    private function generateUniqueCode()
    {
        $code = $this->generateRandomCode(); // Generate a random code initially.
        // Check if the generated code already exists in the database.
        while (Coupon::where('code', $code)->exists()) {
            $code = $this->generateRandomCode(); // Generate a new code if it already exists.
        }

        return $code;
    }

    private function generateRandomCode($length = 7)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    // Frontend
    public function frontendSettings()
    {
        return view('panel.admin.frontend.settings');
    }

    public function frontendSettingsSave(Request $request)
    {

        if (Helper::appIsNotDemo()) {
            $settings = Setting::getCache();
            $settings->site_name = $request->site_name;
            $settings->site_url = substr($request->site_url, -1) === '/' ? substr($request->site_url, 0, -1) : $request->site_url;
            $settings->site_email = $request->site_email;
            $settings->frontend_pricing_section = $request->frontend_pricing_section;
            $settings->frontend_custom_templates_section = $request->frontend_custom_templates_section;
            $settings->frontend_additional_url = $request->frontend_additional_url;
            $settings->frontend_custom_css = $request->frontend_custom_css;
            $settings->frontend_custom_js = $request->frontend_custom_js;
            $settings->frontend_footer_facebook = $request->frontend_footer_facebook;
            $settings->frontend_footer_twitter = $request->frontend_footer_twitter;
            $settings->frontend_footer_instagram = $request->frontend_footer_instagram;
            $settings->frontend_code_before_head = $request->frontend_code_before_head;
            $settings->frontend_code_before_body = $request->frontend_code_before_body;
            $settings->save();

            setting([
                'facebook_domain_verification' => $request->facebook_domain_verification ?: '',
                'google_robots'                => $request->google_robots,
            ])->save();

            $fSettings = FrontendSetting::first();
            $fSettings->header_title = $request->header_title;
            $fSettings->header_text = $request->header_text;

            $fSettings->sign_in = $request->sign_in;
            $fSettings->join_hub = $request->join_hub;

            $fSettings->hero_subtitle = $request->hero_subtitle;
            $fSettings->hero_title = $request->hero_title;
            $fSettings->hero_title_text_rotator = $request->hero_title_text_rotator;
            $fSettings->hero_description = $request->hero_description;
            $fSettings->hero_scroll_text = $request->hero_scroll_text;
            $fSettings->hero_button = $request->hero_button;
            $fSettings->hero_button_url = $request->hero_button_url;
            $fSettings->hero_button_type = $request->hero_button_type;

            if (setting('front_theme') === 'social-media') {
                $fSettings->no_credit_cart_required = $request->no_credit_cart_required;
                $fSettings->faster_content_creation = $request->faster_content_creation;
                $fSettings->over_5000_businesses = $request->over_5000_businesses;
            }

            $fSettings->floating_button_small_text = $request->floating_button_small_text;
            $fSettings->floating_button_bold_text = $request->floating_button_bold_text;
            $fSettings->floating_button_link = $request->floating_button_link;
            $fSettings->floating_button_active = $request->floating_button_active;

            $fSettings->footer_header = $request->footer_header;
            $fSettings->footer_text_small = $request->footer_text_small;
            $fSettings->footer_text = $request->footer_text;
            $fSettings->footer_button_text = $request->footer_button_text;
            $fSettings->footer_button_url = $request->footer_button_url;
            $fSettings->footer_copyright = $request->footer_copyright;
            $fSettings->footer_text_color = $request->footer_text_color;
            // hero_image
            if ($request->hasFile('hero_image')) {
                $path = 'upload/';
                $image = $request->file('hero_image');
                $image_name = Str::random(4) . '-' . Str::slug($settings->site_name) . '-hero-image.' . $image->getClientOriginalExtension();

                $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                    $data = [
                        'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                    ];

                    return response()->json($data, 419);
                }

                $image->move($path, $image_name);
                $fSettings->hero_image = '/' . $path . $image_name;
            }
            $fSettings->save();

            $fSecSettings = FrontendSectionsStatus::first();
            $fSecSettings->preheader_active = $request->preheader_active;
            $fSecSettings->save();

            $logo_types = [
                'logo'                => '',
                'logo_dark'           => 'dark',
                'logo_sticky'         => 'sticky',
                'logo_dashboard'      => 'dashboard',
                'logo_dashboard_dark' => 'dashboard-dark',
                'logo_collapsed'      => 'collapsed',
                'logo_collapsed_dark' => 'collapsed-dark',
                // retina
                'logo_2x'                => '2x',
                'logo_dark_2x'           => 'dark-2x',
                'logo_sticky_2x'         => 'sticky-2x',
                'logo_dashboard_2x'      => 'dashboard-2x',
                'logo_dashboard_dark_2x' => 'dashboard-dark-2x',
                'logo_collapsed_2x'      => 'collapsed-2x',
                'logo_collapsed_dark_2x' => 'collapsed-dark-2x',
            ];

            foreach ($logo_types as $logo => $logo_prefix) {

                if ($request->hasFile($logo)) {
                    $path = 'upload/images/logo/';
                    $image = $request->file($logo);
                    $image_name = Str::random(4) . '-' . $logo_prefix . '-' . Str::slug($settings->site_name) . '-logo.' . $image->getClientOriginalExtension();

                    // Resim uzantı kontrolü
                    $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                    if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                        $data = [
                            'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                        ];

                        return response()->json($data, 419);
                    }

                    $image->move($path, $image_name);

                    $settings->{$logo . '_path'} = $path . $image_name;
                    $settings->{$logo} = $image_name;
                    $settings->save();
                }

            }

            if ($request->hasFile('favicon')) {
                $path = 'upload/images/favicon/';
                $image = $request->file('favicon');
                $image_name = Str::random(4) . '-' . Str::slug($settings->site_name) . '-favicon.' . $image->getClientOriginalExtension();

                // Resim uzantı kontrolü
                $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                    $data = [
                        'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                    ];

                    return response()->json($data, 419);
                }

                $image->move($path, $image_name);

                $settings->favicon_path = $path . $image_name;
                $settings->favicon = $image_name;
                $settings->save();
            }
        }

    }

    // Section Settings
    public function frontendSectionSettings()
    {
        return view('panel.admin.frontend.section_settings');
    }

    public function frontendSectionSettingsSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            $settings = FrontendSectionsStatus::first();
            $settings->features_active = $request->features_active;
            $settings->features_title = $request->features_title;
            $settings->features_subtitle = $request->features_subtitle;
            $settings->features_description = $request->features_description;
            $settings->marquee_items = $request->marquee_items;
            $settings->generators_active = $request->generators_active;
            $settings->generators_subtitle = $request->generators_subtitle;
            $settings->generators_title = $request->generators_title;
            $settings->generators_description = $request->generators_description;

            $settings->who_is_for_active = $request->who_is_for_active;

            $settings->custom_templates_active = $request->custom_templates_active;
            $settings->custom_templates_subtitle_one = $request->custom_templates_subtitle_one;
            $settings->custom_templates_subtitle_two = $request->custom_templates_subtitle_two;
            $settings->custom_templates_title = $request->custom_templates_title;
            $settings->custom_templates_description = $request->custom_templates_description;

            $settings->tools_active = $request->tools_active;
            $settings->tools_title = $request->tools_title;
            $settings->tools_subtitle = $request->tools_subtitle;
            $settings->tools_description = $request->tools_description;

            $settings->custom_templates_learn_more_link = $request->custom_templates_learn_more_link;
            $settings->custom_templates_learn_more_link_url = $request->custom_templates_learn_more_link_url;

            $settings->how_it_works_active = $request->how_it_works_active;
            $settings->how_it_works_title = $request->how_it_works_title;
            $settings->how_it_works_subtitle = $request->how_it_works_subtitle;
            $settings->how_it_works_description = $request->how_it_works_description;
            $settings->how_it_works_link = $request->how_it_works_link;
            $settings->how_it_works_link_label = $request->how_it_works_link_label;

            $settings->testimonials_active = $request->testimonials_active;
            $settings->testimonials_title = $request->testimonials_title;
            $settings->testimonials_description = $request->testimonials_description;
            $settings->testimonials_subtitle_one = $request->testimonials_subtitle_one;
            $settings->testimonials_subtitle_two = $request->testimonials_subtitle_two;

            $settings->pricing_active = $request->pricing_active;
            $settings->pricing_title = $request->pricing_title;
            $settings->pricing_subtitle = $request->pricing_subtitle;
            $settings->pricing_description = $request->pricing_description;
            $settings->pricing_save_percent = $request->pricing_save_percent;

            $settings->faq_active = $request->faq_active;
            $settings->faq_title = $request->faq_title;
            $settings->faq_subtitle = $request->faq_subtitle;
            $settings->faq_text_one = $request->faq_text_one;
            $settings->faq_text_two = $request->faq_text_two;

            $settings->blog_active = $request->blog_active;
            $settings->blog_title = $request->blog_title;
            $settings->blog_subtitle = $request->blog_subtitle;
            $settings->blog_posts_per_page = $request->blog_posts_per_page;
            $settings->blog_button_text = $request->blog_button_text;
            $settings->blog_a_title = $request->blog_a_title;
            $settings->blog_a_subtitle = $request->blog_a_subtitle;
            $settings->blog_a_description = $request->blog_a_description;
            $settings->blog_a_posts_per_page = $request->blog_a_posts_per_page;

            $settings->advanced_features_section_title = $request->advanced_features_section_title;
            $settings->advanced_features_section_description = $request->advanced_features_section_description;

            $settings->plan_footer_text = $request->plan_footer_text;
            $settings->save();

            if (setting('front_theme') === 'social-media') {
                $find = \App\Models\Frontend\FrontendSetting::query()->first();
                $find->join_the_ranks = $request->join_the_ranks;
                $find->save();
            }

            // Handle advanced features section
            $advancedFeaturesKeys = array_filter(array_keys($request->all()), function ($key) {
                return preg_match('/^advanced_features_title_\d+$/', $key);
            });

            foreach ($advancedFeaturesKeys as $titleKey) {
                $key = explode('_', $titleKey)[3];

                // Retrieve the existing feature or create a new one
                $feature = AdvancedFeaturesSection::firstOrNew(['id' => $key]);

                $feature->title = $request->input('advanced_features_title_' . $key);
                $feature->description = $request->input('advanced_features_description_' . $key);

                // Handle image upload
                if ($request->hasFile('advanced_features_image_' . $key)) {
                    $path = 'upload/images/frontent/';
                    $image = $request->file('advanced_features_image_' . $key);
                    $image_name = Str::random(4) . '-' . Str::slug($request->input('advanced_features_title_' . $key)) . '.' . $image->getClientOriginalExtension();

                    // Resim uzantı kontrolü
                    $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                    if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                        $data = [
                            'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                        ];

                        return response()->json($data, 419);
                    }

                    $image->move($path, $image_name);

                    $feature->image = '/' . $path . $image_name;
                }
                $feature->save();
            }

            // comparison section

            $comparison_section_items = array_filter(array_keys($request->all()), function ($key) {
                return preg_match('/^comparison_section_item_label_\d+$/', $key);
            });

            foreach ($comparison_section_items as $comparison_section_item) {
                $key = explode('_', $comparison_section_item)[4];

                $comparison_section = ComparisonSectionItems::query()
                    ->find($key);

                if (! $comparison_section) {
                    continue;
                }

                $comparison_section->update([
                    'label'  => $request->input('comparison_section_item_label_' . $key),
                    'others' => $request->input('comparison_section_item_others_' . $key) == 'true',
                    'ours'   => $request->input('comparison_section_item_ours_' . $key) == 'true',
                ]);
            }

            $features_marquees = array_filter(array_keys($request->all()), function ($key) {
                return preg_match('/^features_marquee_\d+$/', $key);
            });

            foreach ($features_marquees as $features_marquee) {

                $key = explode('_', $features_marquee)[2];

                $features_marquee = FeaturesMarquee::query()
                    ->find($key);

                if (! $features_marquee) {
                    continue;
                }

                $features_marquee->update([
                    'title' => $request->input('features_marquee_' . $key),
                ]);
            }

            $footer_items = array_filter(array_keys($request->all()), function ($key) {
                return preg_match('/^footer_item_\d+$/', $key);
            });

            foreach ($footer_items as $footer_item) {

                $key = explode('_', $footer_item)[2];

                $footer_item = FooterItem::query()
                    ->find($key);

                if (! $footer_item) {
                    continue;
                }

                $footer_item->update([
                    'item' => $request->input('footer_item_' . $key),
                ]);
            }

            $banner_bottom_texts = array_filter(array_keys($request->all()), function ($key) {
                return preg_match('/^banner_bottom_text_\d+$/', $key);
            });

            foreach ($banner_bottom_texts as $banner_bottom_text) {

                $key = explode('_', $banner_bottom_text)[3];

                $banner_bottom_text = BannerBottomText::query()
                    ->find($key);

                if (! $banner_bottom_text) {
                    continue;
                }

                $banner_bottom_text->update([
                    'text' => $request->input('banner_bottom_text_' . $key),
                ]);
            }
        }
    }

    // Menu
    public function menuSettings()
    {
        $menus = Setting::getCache()->menu_options;

        $frontMenus = $menus ?: '[{"title": "Home","url": "#banner","target": false},{"title": "Features","url": "#features","target": false},{"title": "How it Works","url": "#how-it-works","target": false},{"title": "Testimonials","url": "#testimonials","target": false},{"title": "Pricing","url": "#pricing","target": false},{"title": "FAQ","url": "#faq","target": false}]';

        $megaMenus = MarketplaceHelper::isRegistered('mega-menu') ? MegaMenu::query()->where('status', 1)->get() : collect([]);

        return view('panel.admin.frontend.menu', [
            'menus'      => json_decode($frontMenus, true),
            'mega_menus' => $megaMenus,
        ]);
    }

    public function menuSettingsSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            $settings = Setting::getCache();
            $settings->menu_options = $request->menu_options;
            $settings->save();
        }
    }

    public function authSettings()
    {
        $settings = Setting::getCache();
        $auth = json_decode($settings->auth_view_options);

        return view('panel.admin.frontend.auth', compact('auth'));
    }

    public function authSettingsSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            $settings = Setting::getCache();
            $old_auth = json_decode($settings->auth_view_options);

            $auth = [
                'login_enabled' => $request->login_enabled ?? 0,
            ];
            // move image to correct location
            if ($request->hasFile('login_image')) {
                $path = 'upload/images/auth/';
                $image = $request->file('login_image');
                $image_name = Str::random(4) . '-login-image.' . $image->getClientOriginalExtension();
                $image->move($path, $image_name);
                $auth['login_image'] = $path . $image_name;
            } else {
                $auth['login_image'] = $old_auth?->login_image ?? null;
            }

            $settings->auth_view_options = json_encode($auth);
            $settings->save();
        }
    }

    // Frontend Frequently asked questions, FAQ
    public function frontendFaq()
    {
        $faq = Faq::orderBy('created_at', 'desc')->get();

        return view('panel.admin.frontend.faq.index', compact('faq'));
    }

    public function frontendFaqcreateOrUpdate($id = null)
    {
        if ($id == null) {
            $faq = null;
        } else {
            $faq = Faq::where('id', $id)->firstOrFail();
        }

        return view('panel.admin.frontend.faq.form', compact('faq'));
    }

    public function frontendFaqcreateOrUpdateSave(Request $request)
    {
        if ($request->faq_id != 'undefined') {
            $faq = Faq::where('id', $request->faq_id)->firstOrFail();
        } else {
            $faq = new Faq;
        }

        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
    }

    public function frontendFaqDelete($id)
    {
        $faq = Faq::where('id', $id)->firstOrFail();
        $faq->delete();

        return back()->with(['message' => __('Faq deleted succesfully'), 'type' => 'success']);
    }

    // Frontend Tools Section

    public function frontendTools()
    {
        $items = FrontendTools::orderBy('created_at', 'desc')->get();

        return view('panel.admin.frontend.tools.index', compact('items'));
    }

    public function frontendToolscreateOrUpdate($id = null)
    {
        if ($id == null) {
            $item = null;
        } else {
            $item = FrontendTools::where('id', $id)->firstOrFail();
        }

        return view('panel.admin.frontend.tools.form', compact('item'));
    }

    public function frontendToolscreateOrUpdateSave(Request $request)
    {
        if ($request->item_id != 'undefined') {
            $item = FrontendTools::where('id', $request->item_id)->firstOrFail();
        } else {
            $item = new FrontendTools;
        }
        $item->title = $request->title;
        $item->description = $request->description;
        $item->buy_link = $request->buy_link;
        $item->buy_link_url = $request->buy_link_url;
        $item->learn_more_link = $request->learn_more_link;
        $item->learn_more_link_url = $request->learn_more_link_url;

        if ($request->hasFile('image')) {
            $path = 'upload/images/frontent/tools/';
            $image = $request->file('image');
            $image_name = Str::random(4) . '-' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();

            // Resim uzantı kontrolü
            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                $data = [
                    'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                ];

                return response()->json($data, 419);
            }

            $image->move($path, $image_name);

            $item->image = $path . $image_name;
        }

        $item->save();
    }

    public function frontendToolsDelete($id)
    {
        $item = FrontendTools::where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with(['message' => __('Item deleted succesfully'), 'type' => 'success']);
    }

    // Future of ai section
    public function frontendFuture()
    {
        $items = FrontendFuture::orderBy('created_at', 'desc')->get();

        return view('panel.admin.frontend.future.index', compact('items'));
    }

    public function frontendFuturecreateOrUpdate($id = null)
    {
        if ($id == null) {
            $item = null;
        } else {
            $item = FrontendFuture::where('id', $id)->firstOrFail();
        }

        return view('panel.admin.frontend.future.form', compact('item'));
    }

    public function frontendFuturecreateOrUpdateSave(Request $request)
    {
        if ($request->item_id != 'undefined') {
            $item = FrontendFuture::where('id', $request->item_id)->firstOrFail();
        } else {
            $item = new FrontendFuture;
        }
        $item->title = $request->title;
        $item->description = $request->description;
        $item->image = $request->image;
        $item->save();
    }

    public function frontendFutureDelete($id)
    {
        $item = FrontendFuture::where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with(['message' => __('Item deleted succesfully'), 'type' => 'success']);
    }

    // Who is for section
    public function frontendWhois()
    {
        $items = FrontendForWho::orderBy('created_at', 'desc')->get();

        return view('panel.admin.frontend.who_is_for.index', compact('items'));
    }

    public function frontendWhoiscreateOrUpdate($id = null)
    {
        if ($id == null) {
            $item = null;
        } else {
            $item = FrontendForWho::where('id', $id)->firstOrFail();
        }

        return view('panel.admin.frontend.who_is_for.form', compact('item'));
    }

    public function frontendWhoiscreateOrUpdateSave(Request $request)
    {
        if ($request->item_id != 'undefined') {
            $item = FrontendForWho::where('id', $request->item_id)->firstOrFail();
        } else {
            $item = new FrontendForWho;
        }
        $item->title = $request->title;
        $item->color = $request->color;
        $item->save();
    }

    public function frontendWhoisDelete($id)
    {
        $item = FrontendForWho::where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with(['message' => __('Item deleted succesfully'), 'type' => 'success']);
    }

    // Generator list

    public function frontendGeneratorlist()
    {
        $items = FrontendGenerators::orderBy('created_at', 'desc')->get();

        return view('panel.admin.frontend.generators_list.index', compact('items'));
    }

    public function frontendGeneratorlistcreateOrUpdate($id = null)
    {
        if ($id == null) {
            $item = null;
        } else {
            $item = FrontendGenerators::where('id', $id)->firstOrFail();
        }

        return view('panel.admin.frontend.generators_list.form', compact('item'));
    }

    public function frontendGeneratorlistcreateOrUpdateSave(Request $request)
    {
        if ($request->item_id != 'undefined') {
            $item = FrontendGenerators::where('id', $request->item_id)->firstOrFail();
        } else {
            $item = new FrontendGenerators;
        }

        if ($request->hasFile('image')) {
            $path = 'upload/images/generatorlist/';
            $image = $request->file('image');
            $image_name = Str::random(4) . '-' . Str::slug($request->title) . '-image.' . $image->getClientOriginalExtension();

            // Resim uzantı kontrolü
            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                $data = [
                    'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                ];

                return response()->json($data, 419);
            }

            $image->move($path, $image_name);

            $item->image = $path . $image_name;
        }

        $item->menu_title = $request->menu_title;
        $item->subtitle_one = $request->subtitle_one;
        $item->subtitle_two = $request->subtitle_two;
        $item->icon = $request->icon;
        $item->title = $request->title;
        $item->text = $request->text;
        $item->image_title = $request->image_title;
        $item->image_subtitle = $request->image_subtitle;
        $item->color = $request->color;
        $item->save();
    }

    public function frontendGeneratorlistDelete($id)
    {
        $item = FrontendGenerators::where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with(['message' => __('Item deleted succesfully'), 'type' => 'success']);
    }

    // socialmedia, socialmediaSave
    public function socialmedia(): View
    {
        $socialmedia = SocialMediaAccounts::get();

        return view('panel.admin.frontend.socialmedia', compact('socialmedia'));
    }

    public function socialmediaSave(Request $request): RedirectResponse
    {
        if (Helper::appIsNotDemo()) {
            $accounts = SocialMediaAccounts::get();
            foreach ($accounts as $account) {
                $account->subtitle = $request->input('subtitle_' . $account->key);
                $account->icon = $request->input('icon_' . $account->key);
                $account->link = $request->input('link_' . $account->key);
                $account->is_active = $request->has('is_active_' . $account->key);
                $account->save();
            }
        }

        return back()->with(['message' => __('Social media accounts saved successfully.'), 'type' => 'success']);
    }

    public function deletionRequests(): View
    {
        $deletionRequests = AccountDeletionReqs::orderBy('created_at', 'desc')->get();

        return view('panel.admin.settings.deletion_requests', compact('deletionRequests'));
    }

    public function deletionRequest($id)
    {
        if (Helper::appIsNotDemo() && auth()->user()->isAdmin()) {
            $deletionRequest = AccountDeletionReqs::where('user_id', $id)->firstOrFail();
            CreateActivity::for($deletionRequest->user, 'Deleted', $deletionRequest->user?->fullName() . ' deleted his/her account.');
            $deletionRequest->user->delete();

            return response()->json([
                'status'  => true,
                'message' => __('User deleted successfully'),
            ], 200);
        }
    }

    public function userPermissions()
    {
        $role = Role::findByName(Roles::ADMIN->value);
        $permissions = $role->permissions->pluck('name')->toArray();

        return view('panel.admin.users.permissions', compact('permissions'));
    }

    public function userPermissionSave(Request $request): RedirectResponse
    {
        $permissions = $request->input('permissionItems', []);

        $role = Role::findByName(Roles::ADMIN->value);

        $role->syncPermissions($permissions);

        Cache::forget('admin_permissions');

        return back()->with(['message' => __('Saved Successfully'), 'type' => 'success']);
    }
}
