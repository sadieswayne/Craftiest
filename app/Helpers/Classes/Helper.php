<?php

namespace App\Helpers\Classes;

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Enums\Roles;
use App\Models\Currency;
use App\Models\Finance\Subscription;
use App\Models\RateLimit;
use App\Models\Setting;
use App\Models\SettingTwo;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

class Helper
{
    use Traits\HasApiKeys;

    public static function adminPermissions(string $key)
    {
        $permissions = Cache::remember('admin_permissions', 3600 * 24, function () {
            $role = Role::findByName(Roles::ADMIN->value);

            return collect($role->getAllPermissions())->pluck('name')->merge(['admin_dashboard']);
        });

        return $permissions->contains($key);
    }

    public static function getCurrentActiveSubscription($userId = null)
    {
        $userId = $userId ?? Auth::id();

        return once(static function () use ($userId) {
            return Subscription::query()
                ->with('plan')
                ->where('user_id', $userId)
                ->whereIn('stripe_status', [
                    'active',
                    'trialing',
                    'bank_approved',
                    'banktransfer_approved',
                    'bank_renewed',
                    'free_approved',
                    'stripe_approved',
                    'paypal_approved',
                    'iyzico_approved',
                    'paystack_approved',
                ])
                ->first();
        });
    }

    public static function showIntercomForBuyer(array $extensions = []): bool
    {
        if (self::showIntercomForVipMembership()) {
            return false;
        }

        $extensionsCollection = collect(
            $extensions ?: app(ExtensionRepositoryInterface::class)->extensions()
        )
            ->where('is_theme', request()->routeIs('dashboard.admin.themes.*'))
            ->where('licensed', true)
            ->where('price', '>', 0);

        return $extensionsCollection->isNotEmpty();
    }

    public static function showIntercomForVipMembership(): bool
    {
        if (! Auth::user()->isAdmin()) {
            return false;
        }

        if (! Auth::user()?->isSuperAdmin()) {
            if (! Auth::user()->checkPermission('VIP_CHAT_WIDGET')) {
                return false;
            }
        }

        return (bool) Cache::remember('vip_membership', 300, function () {
            $marketSubscription = app(ExtensionRepositoryInterface::class)->subscription()->json();

            return data_get($marketSubscription, 'data.stripe_status') === 'active';
        });
    }

    public static function marketplacePaymentMessage(string $status): string
    {
        return match ($status) {
            'paid'    => __('Your payment has been received successfully.'),
            'pending' => __('Your payment is pending. Please check back later for confirmation. Once processed, you\'ll be able to download the extension.'),
            default   => __('Your payment was unsuccessful. Please try again'),
        };
    }

    public static function hasRoute($route = null): bool
    {
        if ($route && Route::has($route)) {
            return true;
        }

        return false;
    }

    public static function arrayMerge($merge = true, array $array = [], array ...$mergeArray): array
    {
        if ($merge) {
            return array_merge($array, ...$mergeArray);
        }

        return $array;
    }

    public static function decodePaymentToken(string $token): array
    {
        $base64 = base64_decode($token);

        $data = explode(':', $base64);

        [$domain, $slug, $license] = $data;

        return compact('domain', 'slug', 'license');
    }

    public static function setEnv(array $data = []): void
    {
        $envPath = base_path('.env');

        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $keyPattern = preg_quote($key, '/');
            $pattern = "/^{$keyPattern}=[^\r\n]*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}=" . '"' . "{$value}" . '"', $content);
            } else {
                $content .= "\n{$key}=" . '"' . "{$value}" . '"';
            }
        }

        file_put_contents($envPath, $content);
    }

    public static function dbConnectionStatus(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function sortingOpenAiSelected($data, $selected = null)
    {
        $data = $data->map(function ($item) use ($selected) {
            $item->selected = 0;
            if ($selected && in_array($item->slug, $selected, true)) {
                $item->selected = 1;
            }

            return $item;
        });

        return $data->sortByDesc('selected');
    }

    public static function strip_all_tags($text, $remove_breaks = false)
    {
        if (is_null($text)) {
            return '';
        }

        if (! is_scalar($text)) {
            /*
             * To maintain consistency with pre-PHP 8 error levels,
             * trigger_error() is used to trigger an E_USER_WARNING,
             * rather than _doing_it_wrong(), which triggers an E_USER_NOTICE.
             */
            trigger_error(
                sprintf(
                    /* translators: 1: The function name, 2: The argument number, 3: The argument name, 4: The expected type, 5: The provided type. */
                    __('Warning: %1$s expects parameter %2$s (%3$s) to be a %4$s, %5$s given.'),
                    __FUNCTION__,
                    '#1',
                    '$text',
                    'string',
                    gettype($text)
                ),
                E_USER_WARNING
            );

            return '';
        }

        $text = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $text);
        $text = strip_tags($text);

        if ($remove_breaks) {
            $text = preg_replace('/[\r\n\t ]+/', ' ', $text);
        }

        return trim($text);
    }

    public static function multi_explode($delimiters, $string): array
    {

        $ready = str_replace($delimiters, $delimiters[0], $string);

        $ready = str_replace([',,'], ',', $ready);

        $ready = str_replace('-', '', $ready);

        return explode($delimiters[0], $ready);
    }

    public static function settingTwo(string $key, $default = null)
    {
        $setting = SettingTwo::getCache();

        return $setting?->getAttribute($key) ?? $default;
    }

    public static function setting(string $key, $default = null, $setting = null)
    {
        $setting = $setting ?: Setting::getCache();

        return $setting?->getAttribute($key) ?? $default;
    }

    public static function appIsDemoForChatbot(): bool
    {
        return self::appIsDemo() && in_array(request()->getHost(), ['magicai.test', 'magicai.liquid-themes.com']);
    }

    public static function appIsDemo(): bool
    {
        return config('app.status') === 'Demo';
    }

    public static function appIsNotDemo(): bool
    {
        return config('app.status') !== 'Demo';
    }

    public static function checkImageDailyLimit()
    {
        $settings_two = SettingTwo::getCache();

        if ($settings_two->daily_limit_enabled) {
            if (Helper::appIsDemo()) {
                $msg = __('You have reached the maximum number of image generation allowed on the demo.');
            } else {
                $msg = __('You have reached the maximum number of image generation allowed.');
            }
            $ipAddress = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : request()->ip();
            $db_ip_address = RateLimit::where('ip_address', $ipAddress)->where('type', 'image')->first();
            if ($db_ip_address) {
                if (now()->diffInDays(Carbon::parse($db_ip_address->last_attempt_at)->format('Y-m-d')) > 0) {
                    $db_ip_address->attempts = 0;
                }
            } else {
                $db_ip_address = new RateLimit(['ip_address' => $ipAddress]);
            }

            if ($db_ip_address->attempts >= $settings_two->allowed_images_count) {
                $data = [
                    'errors' => [$msg],
                ];

                return response()->json($data, 429);
            } else {
                $db_ip_address->attempts++;
                $db_ip_address->last_attempt_at = now();
                $db_ip_address->save();
            }
        }

        return response()->json([], 200);
    }

    public static function checkRemainingImages($usr = null)
    {
        $user = $usr ?? auth()->user();
        if ($user->getAttribute('team')) {
            $teamManager = $user->teamManager;
            if ($teamManager) {
                if ($teamManager->remaining_images <= 0 and $teamManager->remaining_images != -1) {
                    $data = [
                        'errors' => [__('You have no credits left. Please consider upgrading your plan.')],
                    ];

                    return response()->json($data, 429);
                }
            }
            $member = $user->teamMember;
            if ($member) {
                if (! $member->allow_unlimited_credits) {
                    if ($member->remaining_images <= 0 and $member->remaining_images != -1) {
                        $data = [
                            'errors' => [__('You have no credits left. Please consider upgrading your plan.')],
                        ];

                        return response()->json($data, 429);
                    }
                }
            }
        } else {
            if ($user->remaining_images <= 0 and $user->remaining_images != -1) {
                $data = [
                    'errors' => [__('You have no credits left. Please consider upgrading your plan.')],
                ];

                return response()->json($data, 429);
            }
        }

        return response()->json([], 200);
    }

    public static function sorting(array $data, $column, $direction): array|Collection
    {
        if ($column) {
            $data = collect($data)->sortBy($column, SORT_REGULAR, $direction == 'asc');
        }

        return $data;
    }

    public static function parseUrl(string ...$urls): string
    {
        return collect($urls)->map(fn ($url) => trim($url, '/'))->implode('/');
    }

    public static function findCurrencyFromId(null|int|string $id)
    {
        return Currency::cacheFromSetting((string) $id);
    }

    public static function defaultCurrency($column = 'code')
    {
        $currency = Currency::query()->where('id', Helper::setting('default_currency'))->first();

        if (is_null($column)) {
            return $currency;
        }

        return $currency->$column;
    }
}
