<?php

namespace App\Services\Dashboard;

use App\Enums\Plan\FrequencyEnum;
use App\Models\Activity;
use App\Models\Usage;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserOpenai;
use App\Models\UserOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public int $cacheTtl = 300;

    public array $userActiveOrderStatuses = ['Success', 'Approved'];

    public array $frequencies = [
        'monthly'          => 0,
        'yearly'           => 0,
        'lifetime_monthly' => 0,
        'lifetime_yearly'  => 0,
        'prepaid'          => 0,
    ];

    public array $randomColors = ['#74DB84', '#74A9DB', '#DB9374', '#8185F44D', '#E3E8E8', '#C674DB'];

    public function latestOrders(): Collection|array
    {
        return UserOrder::query()
            ->with('user', 'plan')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function activity(): Collection|array
    {
        return Activity::query()
            ->with('user:id,name,surname,avatar')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function setCache(): void
    {
        $this->setDailySales()
            ->setTopCountries()
            ->setDailyUsages()
            ->setDailyUsers()
            ->setPopularPlansData()
            ->setMostUsedLastOpenAITools()
            ->setUserBehaviorData()
            ->setUserBehaviorData()
            ->setUsage();
    }

    public function setUsage(): void
    {
        $usage = $this->cache('instance_usage', function () {
            return Usage::getSingle();
        });

        Cache::putMany([
            'sales_this_week'      => $usage->this_week_sales,
            'sales_previous_week'  => $usage->last_week_sales,
            'words_this_week'      => $usage->this_week_word_count,
            'words_previous_week'  => $usage->last_week_word_count,
            'images_this_week'     => $usage->this_week_image_count,
            'images_previous_week' => $usage->last_week_image_count,
            'users_this_week'      => $usage->this_week_user_count,
            'users_previous_week'  => $usage->last_week_user_count,
            'total_sales'          => $usage->total_sales,
            'total_usage'          => $usage->total_word_count + $usage->total_image_count,
            'total_users'          => $usage->total_user_count,
        ], $this->cacheTtl);
    }

    public function setUserBehaviorData(): static
    {
        $this->cache('user_behavior_data', function () {
            $activities = UserActivity::query()->select('connection')->get();

            $mobileCount = $activities->filter(fn ($activity) => $this->isMobileDevice($activity->connection))->count();

            $desktopCount = $activities->count() - $mobileCount;

            return [
                [
                    'label' => 'Mobile',
                    'value' => $mobileCount,
                    'color' => 'hsl(var(--primary))',
                ],
                [
                    'label' => 'Desktop',
                    'value' => $desktopCount,
                    'color' => 'hsl(var(--secondary))',
                ],
            ];
        });

        return $this;
    }

    private function isMobileDevice($userAgent): bool
    {
        $pattern = '/Mobile|Android|Silk\/|Kindle|BlackBerry|Opera Mini|Opera Mobi/i';

        return (bool) preg_match($pattern, $userAgent);
    }

    public function setMostUsedLastOpenAITools(): static
    {
        $this->cache('popular_tools_data', function () {
            $userTotalOpenAICount = UserOpenai::query()->count('id');

            return UserOpenai::query()
                ->with('generator')
                ->select('openai_id', DB::raw('COUNT(*) as total'))
                ->groupBy('openai_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get()
                ->map(function (UserOpenai $item, $key) use ($userTotalOpenAICount) {
                    $color = $this->randomColors[$key] ?? '#000000';

                    $openai = $item->getAttribute('generator');

                    $title = $openai ? $openai->getAttribute('title') : 'Unknown';

                    $percentage = $userTotalOpenAICount ? round(($item->getAttribute('total') / $userTotalOpenAICount) * 100) : 0;

                    return [
                        'label' => $title,
                        'value' => $percentage,
                        'color' => $color,
                    ];
                });
        });

        return $this;
    }

    public function setPopularPlansData(): static
    {
        $this->cache('popular_plans_data', function () {
            $approvedOrders = UserOrder::query()
                ->with('plan')
                ->whereIn('status', $this->userActiveOrderStatuses)
                ->get();

            // Başlangıçta tüm frekansları 0 olarak başlatıyoruz.
            $plan_counts = array_fill_keys(array_keys($this->frequencies), 0);

            foreach ($approvedOrders as $order) {
                $plan = $order->plan;

                if ($plan) {
                    $key = ($plan->type === 'subscription') ? $plan->frequency : $plan->type;

                    if (isset($plan_counts[$key])) {
                        $plan_counts[$key]++;
                    }
                }
            }

            $plan_names_colors = $this->planLabelsAndColors();
            $popular_plans_data = [];

            foreach ($plan_counts as $key => $count) {
                $popular_plans_data[] = [
                    'label' => $plan_names_colors[$key]['label'],
                    'value' => $count,
                    'color' => $count > 0 ? $plan_names_colors[$key]['color'] : '#2C36490D',
                ];
            }

            return $popular_plans_data;
        });

        return $this;
    }

    public function setDailyUsers(): static
    {
        $this->cache('daily_users', function () {
            return User::query()
                ->select(DB::raw('count(*) as total'), DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as days"))
                ->groupBy('days')
                ->get()
                ->toJson();
        });

        return $this;
    }

    public function setDailyUsages(): static
    {
        $this->cache('daily_usages', function () {
            return UserOpenai::query()
                ->select(
                    DB::raw('SUM(IF(credits=1,credits,0)) as sumsImage'),
                    DB::raw('SUM(IF(credits>1,credits,0)) as sumsWord'),
                    DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as days")
                )->groupBy('days')
                ->get();
        });

        return $this;
    }

    public function setTopCountries(): static
    {
        $this->cache('top_countries', function () {
            return User::query()
                ->select('country', DB::raw('count(*) as total'))
                ->groupBy('country')
                ->get()
                ->toJson();
        });

        return $this;
    }

    public function setDailySales(): static
    {
        $this->cache('daily_sales', function () {
            return UserOrder::query()
                ->select(DB::raw('sum(price) as sums'), DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as days"))
                ->groupBy('days')
                ->get()
                ->toJson();
        });

        return $this;
    }

    public function cache($key, $value)
    {
        return cache()->remember($key, $this->cacheTtl, $value);
    }

    public function planLabelsAndColors(): array
    {
        return [
            FrequencyEnum::MONTHLY->value => [
                'label' => FrequencyEnum::MONTHLY->label(),
                'color' => FrequencyEnum::MONTHLY->color(),
            ],
            FrequencyEnum::YEARLY->value => [
                'label' => FrequencyEnum::YEARLY->label(),
                'color' => FrequencyEnum::YEARLY->color(),
            ],
            FrequencyEnum::LIFETIME_MONTHLY->value => [
                'label' => FrequencyEnum::LIFETIME_MONTHLY->label(),
                'color' => FrequencyEnum::LIFETIME_MONTHLY->color(),
            ],
            FrequencyEnum::LIFETIME_YEARLY->value => [
                'label' => FrequencyEnum::LIFETIME_YEARLY->label(),
                'color' => FrequencyEnum::LIFETIME_YEARLY->color(),
            ],
            'prepaid' => [
                'label' => 'Prepaid',
                'color' => '#60CAF94D',
            ],
        ];
    }
}
