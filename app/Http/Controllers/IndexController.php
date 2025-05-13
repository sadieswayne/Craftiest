<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Clients;
use App\Models\CustomSettings;
use App\Models\Faq;
use App\Models\Frontend\FrontendSectionsStatus;
use App\Models\FrontendForWho;
use App\Models\FrontendFuture;
use App\Models\FrontendGenerators;
use App\Models\FrontendTools;
use App\Models\HowitWorks;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorFilter;
use App\Models\Setting;
use App\Models\Testimonials;
use App\Services\Finance\PlanService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class IndexController extends Controller
{
    public function __construct(private readonly PlanService $planService) {}

    public function __invoke()
    {
        $maintenance = cache()->get('maintenance');

        $maintenanceMode = data_get($maintenance, 'maintenance_mode', false);

        $maintenanceModeAuth = true;

        if (Auth::check()) {
            $maintenanceModeAuth = ! Auth::user()->isAdmin();
        }

        if ($maintenanceMode && $maintenanceModeAuth) {
            return view('maintenance.index', ['data' => $maintenance]);
        }

        $filters = OpenaiGeneratorFilter::all();
        $templates = OpenAIGenerator::all();
        $plansSubscription = $this->planService->getSubscriptionPlans();
        $plansSubscriptionMonthly = $this->planService->getMonthlySubscriptions();
        $plansSubscriptionLifetime = $this->planService->getLifetimeSubscriptions();
        $plansSubscriptionAnnual = $this->planService->getAnnualSubscriptions();
        $plansPrepaid = $this->planService->getPrepaidPlans();
        $faq = Faq::all();
        $tools = FrontendTools::all();
        $futures = FrontendFuture::all();
        $testimonials = Testimonials::all();
        $howitWorks = HowitWorks::orderBy('order', 'ASC')->limit(3)->get();
        $howitWorksDefaults = self::howitWorksDefaults();
        $clients = Clients::all();
        $who_is_for = FrontendForWho::all();
        $generatorsList = FrontendGenerators::orderBy('created_at', 'desc')->get();

        $posts = Blog::where('status', 1)->orderBy('id', 'desc')->paginate(FrontendSectionsStatus::first()->blog_posts_per_page ?? 3);

        $setting = Setting::getCache();

        if ($setting->frontend_additional_url != null) {
            return Redirect::to($setting->frontend_additional_url);
        }

        $currency = currency()->symbol;

        return view('index', compact(
            'templates',
            'plansPrepaid',
            'plansSubscription',
            'filters',
            'faq',
            'tools',
            'testimonials',
            'howitWorks',
            'howitWorksDefaults',
            'clients',
            'futures',
            'who_is_for',
            'generatorsList',
            'plansSubscriptionMonthly',
            'plansSubscriptionLifetime',
            'plansSubscriptionAnnual',
            'posts',
            'currency'
        ));
    }

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
}
