<?php

use App\Enums\Plan\FrequencyEnum;
use App\Enums\Plan\TypeEnum;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Finance\PaymentProcessController;
use App\Models\Coupon;
use App\Models\Finance\YokassaSubscription;
use App\Models\Gateways;
use App\Models\PaymentProof;
use App\Models\Plan;
use App\Models\PrivacyTerms;
use App\Models\UserOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function getImageUrlByOrderId($orderId)
{
    $paymentProof = PaymentProof::where('order_id', $orderId)->first();

    if ($paymentProof) {
        return asset('proofs/' . $paymentProof->proof_image);
    }

    return null;
}

function bankActive()
{
    return Gateways::query()
        ->where('code', 'banktransfer')
        ->where('is_active', 1)
        ->exists();
}

function countBankTansactions()
{
    $bankOrdersCount = UserOrder::where('payment_type', 'banktransfer')->where('status', 'Waiting')->count();

    return $bankOrdersCount;
}

function getCurrentActiveSubscription($userId = null)
{
    return Helper::getCurrentActiveSubscription($userId);
}

function formatPrice($price)
{
    return $price == intval($price) ? number_format($price, 0) : number_format($price, 2);
}

function getCurrentActiveSubscriptionYokkasa($userid = null)
{
    if (Helper::appIsDemo()) {
        return null;
    }
    $userID = $userid ?? auth()->user()->id;

    return once(static function () use ($userID) {
        return YokassaSubscription::query()
            ->where('user_id', $userID)
            ->whereIn('subscription_status', [
                'active',
                'yokassa_approved',
            ])
            ->first();
    });
}

function getTokenPlans()
{
    return Plan::where('type', 'prepaid')->where('active', 1)->get();
}
function getSubsPlans()
{
    return Plan::where('type', TypeEnum::SUBSCRIPTION->value)
        ->where('active', 1)
        ->where(function ($query) {
            $query->where('price', 0)
                ->orWhere('frequency', FrequencyEnum::LIFETIME_MONTHLY)
                ->orWhere('frequency', FrequencyEnum::LIFETIME_YEARLY);
        })
        ->get();
}

function formatCamelCase($input)
{
    $formatted = ucwords(str_replace('_', ' ', $input));

    return $formatted;
}

function checkCouponInRequest($code = null)
{
    if ($code !== null) {
        $coupon = Coupon::where('code', $code)->first();
    } else {
        $couponCode = request()->input('coupon');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
        } else {
            $coupon = null;
        }
    }

    return $coupon;
}

function taxToVal($price, $tax)
{
    $tax_with_val = ($tax > 0) ? ($price * $tax / 100) : 0;

    return $tax_with_val;
}

function displayCurr($symbol, $price, $taxValue = 0, $discountedprice = null)
{
    try {
        if (in_array($symbol, config('currency.currencies_with_right_symbols'))) {
            if ($discountedprice !== null && $discountedprice < $price) {
                return "<span style='text-decoration: line-through; color:red;'>" . $price + $taxValue . $symbol . '</span> <b>' . $discountedprice + $taxValue . $symbol . '</b>';
            } else {
                return '<b>' . $price + $taxValue . $symbol . '</b>';
            }
        } else {
            if ($discountedprice !== null && $discountedprice < $price) {
                return "<span style='text-decoration: line-through; color:red;'>" . $symbol . $price + $taxValue . '</span> <b>' . $symbol . $discountedprice + $taxValue . '</b>';
            } else {
                return '<b>' . $symbol . $price + $taxValue . '</b>';
            }
        }
    } catch (\Exception $th) {

        $discountedprice = (float) str_replace(',', '', $discountedprice);
        $price = (float) str_replace(',', '', $price);
        if (in_array($symbol, config('currency.currencies_with_right_symbols'))) {
            if ($discountedprice !== null && $discountedprice < $price) {
                return "<span style='text-decoration: line-through; color:red;'>" . $price + $taxValue . $symbol . '</span> <b>' . $discountedprice + $taxValue . $symbol . '</b>';
            } else {
                return '<b>' . $price + $taxValue . $symbol . '</b>';
            }
        } else {
            if ($discountedprice !== null && $discountedprice < $price) {
                return "<span style='text-decoration: line-through; color:red;'>" . $symbol . $price + $taxValue . '</span> <b>' . $symbol . $discountedprice + $taxValue . '</b>';
            } else {
                return '<b>' . $symbol . $price + $taxValue . '</b>';
            }
        }
    }

}

function displayCurrPlan($symbol, $price, $discountedprice = null)
{
    if (in_array($symbol, config('currency.currencies_with_right_symbols'))) {
        if ($discountedprice !== null && $discountedprice < $price) {
            return "<small class='inline-flex mb-[0.3em] font-normal text-[0.35em]'><span style='text-decoration: line-through;'>" . $price . '</span>' . $symbol . '</small>&nbsp;' . $discountedprice . "<small class='inline-flex mb-[0.3em] font-normal text-[0.35em]'>" . $symbol . '</small>';
        } else {
            return '<span>' . $price . "</span> <small class='inline-flex mb-[0.3em] font-normal text-[0.35em]'>" . $symbol . '</small>';
        }
    } else {
        if ($discountedprice !== null && $discountedprice < $price) {
            return "<small class='inline-flex mb-[0.3em] font-normal text-[0.35em]'>" . $symbol . "<span style='text-decoration: line-through;'>" . $price . "</span> </small>&nbsp; <small class='inline-flex mb-[0.3em] font-normal text-[0.35em]'>" . $symbol . '</small>' . $discountedprice;
        } else {
            return "<small class='inline-flex mb-[0.3em] font-normal text-[0.35em]'>" . $symbol . '</small> <span>' . $price . '</span>';
        }
    }
}

function activeRoute(...$route_name): string
{

    $exceptListSlugs = [
        'ai_pdf', 'ai_vision', 'ai_chat_image', 'ai_code_generator', 'ai_youtube', 'ai_rss',
    ];

    $slug = request()->route('slug');

    if ($slug && in_array($slug, $exceptListSlugs)) {
        return '';
    }

    return request()->routeIs(...$route_name) ? 'active' : '';
}

function activeRouteBulk(...$route_names)
{
    return request()->routeIs(...$route_names) ? 'active' : '';
}

function activeRouteBulkShow(...$route_names)
{
    return request()->routeIs(...$route_names) ? 'show' : '';
}

if (! function_exists('custom_theme_url')) {
    function custom_theme_url($url, $slash = false)
    {
        if (strpos($url, 'assets') !== false) {
            return theme_url($url);
        }
        if ($slash) {
            return '/' . $url;
        }

        return $url;
    }
}

if (! function_exists('get_theme')) {
    function get_theme()
    {
        $theme = \Theme::get();
        if (! $theme) {
            $theme = 'default';
        }

        return $theme;
    }
}
// hsl_to_hex
if (! function_exists('hsl_to_hex')) {
    function hsl_to_hex($h, $s, $l)
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ($s === 0) {
            $r = $g = $b = $l;
        } else {
            $hTemp = function ($p, $q, $t) {
                if ($t < 0) {
                    $t += 1;
                }
                if ($t > 1) {
                    $t -= 1;
                }
                if ($t < 1 / 6) {
                    return $p + ($q - $p) * 6 * $t;
                }
                if ($t < 1 / 2) {
                    return $q;
                }
                if ($t < 2 / 3) {
                    return $p + ($q - $p) * (2 / 3 - $t) * 6;
                }

                return $p;
            };

            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $hTemp($p, $q, $h + 1 / 3);
            $g = $hTemp($p, $q, $h);
            $b = $hTemp($p, $q, $h - 1 / 3);
        }

        $r = round($r * 255);
        $g = round($g * 255);
        $b = round($b * 255);

        $hex = sprintf('#%02x%02x%02x', $r, $g, $b);

        return $hex;
    }
}
// hex_to_hsl
if (! function_exists('hex_to_hsl')) {
    function hex_to_hsl($hex)
    {
        // Remove '#' if present
        $hex = str_replace('#', '', $hex);

        // Convert hex to RGB
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        // Find the maximum and minimum values
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        // Calculate the lightness
        $l = ($max + $min) / 2;

        if ($max === $min) {
            $h = $s = 0; // achromatic
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);

                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;

                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;

                    break;
            }
            $h /= 6;
        }

        // Convert to percentage
        $h = round($h * 360);
        $s = round($s * 100);
        $l = round($l * 100);

        return "$h, $s%, $l%";
    }
}

function percentageChange($old, $new, int $precision = 1)
{
    if ($old == 0) {
        $old++;
        $new++;
    }
    $change = round((($new - $old) / $old) * 100, $precision);

    // Limiting the percentage change to be between 0 and 100
    $change = max(0, min(100, $change));

    return $change;
}

function percentageChangeSign($old, $new, int $precision = 2)
{

    if (percentageChange($old, $new) > 0) {
        return 'plus';
    } else {
        return 'minus';
    }
}

function currency()
{
    $setting = \App\Models\Setting::getCache();

    $currency = \App\Models\Currency::cacheFromSetting($setting->default_currency);

    if (in_array($currency->code, config('currency.needs_code_with_symbol'), true)) {
        $currency->symbol = $currency->code . ' ' . $currency->symbol;
    }

    return $currency;
}

function getSubscription()
{
    $activeSub = getCurrentActiveSubscription();
    if ($activeSub == null) {
        $activeSub = getCurrentActiveSubscriptionYokkasa();
    }

    return $activeSub;
}

function getSubscriptionActive()
{
    return getSubscription();
}

function getSubscriptionStatus()
{
    return PaymentProcessController::getSubscriptionStatus();
}

function checkIfTrial()
{
    return PaymentProcessController::checkIfTrial();
}

function getSubscriptionName()
{
    $subscription = getSubscription();

    return $subscription !== null ? Plan::query()->where('id', $subscription->name)->first()?->name : '';
}

function getYokassaSubscriptionName()
{
    $user = Auth::user();

    return Plan::where('id', getYokassaSubscription()->plan_id)->first()->plan_id;
}

function getSubscriptionRenewDate()
{
    return PaymentProcessController::getSubscriptionRenewDate();
}

function getSubscriptionDaysLeft()
{
    return PaymentProcessController::getSubscriptionDaysLeft();
}

// Templates favorited
function isFavorited($template_id)
{
    $isFav = \App\Models\UserFavorite::where('user_id', Auth::id())->where('openai_id', $template_id)->exists();

    return $isFav;
}

// Docs favorited
function isFavoritedDoc($template_id)
{
    $isFav = \App\Models\UserDocsFavorite::where('user_id', Auth::id())->where('user_openai_id', $template_id)->exists();

    return $isFav;
}

// Country Flags
function country2flag(string $countryCode): string
{

    if (strpos($countryCode, '-') !== false) {
        $countryCode = substr($countryCode, strpos($countryCode, '-') + 1);
    } elseif (strpos($countryCode, '_') !== false) {
        $countryCode = substr($countryCode, strpos($countryCode, '_') + 1);
    }

    if ($countryCode === 'el') {
        $countryCode = 'gr';
    } elseif ($countryCode === 'da') {
        $countryCode = 'dk';
    }

    return (string) preg_replace_callback(
        '/./',
        static fn (array $letter) => mb_chr(ord($letter[0]) % 32 + 0x1F1E5),
        $countryCode
    );
}

// Memory Limit
function getServerMemoryLimit()
{
    return (int) ini_get('memory_limit');
}

// Count Words
function countWords($text)
{

    $encoding = mb_detect_encoding($text);

    if ($encoding === 'UTF-8') {
        // Count Chinese words by splitting the string into individual characters
        $words = preg_match_all('/\p{Han}|\p{L}+|\p{N}+/u', $text);
    } else {
        // For other languages, use str_word_count()
        $words = str_word_count($text, 0, $encoding);
    }

    return (int) $words;

}

function getDefinedLangs()
{
    $fields = \DB::getSchemaBuilder()->getColumnListing('strings');
    $exceptions = ['en', 'code', 'created_at', 'updated_at'];
    $filtered = collect($fields)->filter(function ($value, $key) use ($exceptions) {
        if (! in_array($value, $exceptions)) {
            return $value;
        }
    });

    return $filtered->all();
}

function getVoiceNames($hash)
{
    $voiceNames = [
        'af-ZA-Standard-A'  => 'Ayanda (' . __('Female') . ')',
        'ar-XA-Standard-A'  => 'Fatima (' . __('Female') . ')',
        'ar-XA-Standard-B'  => 'Ahmed (' . __('Male') . ')',
        'ar-XA-Standard-C'  => 'Mohammed (' . __('Male') . ')',
        'ar-XA-Standard-D'  => 'Aisha (' . __('Female') . ')',
        'ar-XA-Wavenet-A'   => 'Layla (' . __('Female') . ')',
        'ar-XA-Wavenet-B'   => 'Ali (' . __('Male') . ')',
        'ar-XA-Wavenet-C'   => 'Omar (' . __('Male') . ')',
        'ar-XA-Wavenet-D'   => 'Zahra (' . __('Female') . ')',
        'eu-ES-Standard-A'  => 'Ane (' . __('Female') . ')',
        'bn-IN-Standard-A'  => 'Ananya (' . __('Female') . ')',
        'bn-IN-Standard-B'  => 'Aryan (' . __('Male') . ')',
        'bn-IN-Wavenet-A'   => 'Ishita (' . __('Female') . ')',
        'bn-IN-Wavenet-B'   => 'Arry (' . __('Male') . ')',
        'bg-BG-Standard-A'  => 'Elena (' . __('Female') . ')',
        'ca-ES-Standard-A'  => 'Laia (' . __('Female') . ')',
        'yue-HK-Standard-A' => 'Wing (' . __('Female') . ')',
        'yue-HK-Standard-B' => 'Ho (' . __('Male') . ')',
        'yue-HK-Standard-C' => 'Siu (' . __('Female') . ')',
        'yue-HK-Standard-D' => 'Lau (' . __('Male') . ')',
        'cs-CZ-Standard-A'  => 'Tereza (' . __('Female') . ')',
        'cs-CZ-Wavenet-A'   => 'Karolína (' . __('Female') . ')',
        // "da-DK-Neural2-D" => "Neural2 - FEMALE",
        // "da-DK-Neural2-F" => "Neural2 - MALE",
        'da-DK-Standard-A' => 'Emma (' . __('Female') . ')',
        'da-DK-Standard-A' => 'Freja (' . __('Female') . ')',
        'da-DK-Standard-A' => 'Ida (' . __('Female') . ')',
        'da-DK-Standard-C' => 'Noah (' . __('Male') . ')',
        'da-DK-Standard-D' => 'Mathilde (' . __('Female') . ')',
        'da-DK-Standard-E' => 'Clara (' . __('Female') . ')',
        'da-DK-Wavenet-A'  => 'Isabella (' . __('Female') . ')',
        'da-DK-Wavenet-C'  => 'Lucas (' . __('Male') . ')',
        'da-DK-Wavenet-D'  => 'Olivia (' . __('Female') . ')',
        'da-DK-Wavenet-E'  => 'Emily (' . __('Female') . ')',
        'nl-BE-Standard-A' => 'Emma (' . __('Female') . ')',
        'nl-BE-Standard-B' => 'Thomas (' . __('Male') . ')',
        'nl-BE-Wavenet-A'  => 'Sophie (' . __('Female') . ')',
        'nl-BE-Wavenet-B'  => 'Lucas (' . __('Male') . ')',
        'nl-NL-Standard-A' => 'Emma (' . __('Female') . ')',
        'nl-NL-Standard-B' => 'Daan (' . __('Male') . ')',
        'nl-NL-Standard-C' => 'Luuk (' . __('Male') . ')',
        'nl-NL-Standard-D' => 'Lotte (' . __('Female') . ')',
        'nl-NL-Standard-E' => 'Sophie (' . __('Female') . ')',
        'nl-NL-Wavenet-A'  => 'Mila (' . __('Female') . ')',
        'nl-NL-Wavenet-B'  => 'Sem (' . __('Male') . ')',
        'nl-NL-Wavenet-C'  => 'Stijn (' . __('Male') . ')',
        'nl-NL-Wavenet-D'  => 'Fenna (' . __('Female') . ')',
        'nl-NL-Wavenet-E'  => 'Eva (' . __('Female') . ')',
        // "en-AU-Neural2-A" => "Neural2 - FEMALE",
        // "en-AU-Neural2-B" => "Neural2 - MALE",
        // "en-AU-Neural2-C" => "Neural2 - FEMALE",
        // "en-AU-Neural2-D" => "Neural2 - MALE",
        'en-AU-News-E'     => 'Emma (' . __('Female') . ')',
        'en-AU-News-F'     => 'Olivia (' . __('Female') . ')',
        'en-AU-News-G'     => 'Liam (' . __('Male') . ')',
        'en-AU-Polyglot-1' => 'Noah (' . __('Male') . ')',
        'en-AU-Standard-A' => 'Charlotte (' . __('Female') . ')',
        'en-AU-Standard-B' => 'Oliver (' . __('Male') . ')',
        'en-AU-Standard-C' => 'Ava (' . __('Female') . ')',
        'en-AU-Standard-D' => 'Jack (' . __('Male') . ')',
        'en-AU-Wavenet-A'  => 'Sophie (' . __('Female') . ')',
        'en-AU-Wavenet-B'  => 'William (' . __('Male') . ')',
        'en-AU-Wavenet-C'  => 'Amelia (' . __('Female') . ')',
        'en-AU-Wavenet-D'  => 'Thomas (' . __('Male') . ')',
        'en-IN-Standard-A' => 'Aditi (' . __('Female') . ')',
        'en-IN-Standard-B' => 'Arjun (' . __('Male') . ')',
        'en-IN-Standard-C' => 'Rohan (' . __('Male') . ')',
        'en-IN-Standard-D' => 'Ananya (' . __('Female') . ')',
        'en-IN-Wavenet-A'  => 'Alisha (' . __('Female') . ')',
        'en-IN-Wavenet-B'  => 'Aryan (' . __('Male') . ')',
        'en-IN-Wavenet-C'  => 'Kabir (' . __('Male') . ')',
        'en-IN-Wavenet-D'  => 'Diya (' . __('Female') . ')',
        // "en-GB-Neural2-A" => "Neural2 - FEMALE",
        // "en-GB-Neural2-B" => "Neural2 - MALE",
        // "en-GB-Neural2-C" => "Neural2 - FEMALE",
        // "en-GB-Neural2-D" => "Neural2 - MALE",
        // "en-GB-Neural2-F" => "Neural2 - FEMALE",
        'en-GB-News-G'     => 'Amelia (' . __('Female') . ')',
        'en-GB-News-H'     => 'Elise (' . __('Female') . ')',
        'en-GB-News-I'     => 'Isabella (' . __('Female') . ')',
        'en-GB-News-J'     => 'Jessica (' . __('Female') . ')',
        'en-GB-News-K'     => 'Alexander (' . __('Male') . ')',
        'en-GB-News-L'     => 'Benjamin (' . __('Male') . ')',
        'en-GB-News-M'     => 'Charles (' . __('Male') . ')',
        'en-GB-Standard-A' => 'Emily (' . __('Female') . ')',
        'en-GB-Standard-B' => 'John (' . __('Male') . ')',
        'en-GB-Standard-C' => 'Mary (' . __('Female') . ')',
        'en-GB-Standard-D' => 'Peter (' . __('Male') . ')',
        'en-GB-Standard-F' => 'Sarah (' . __('Female') . ')',
        'en-GB-Wavenet-A'  => 'Ava (' . __('Female') . ')',
        'en-GB-Wavenet-B'  => 'David (' . __('Male') . ')',
        'en-GB-Wavenet-C'  => 'Emily (' . __('Female') . ')',
        'en-GB-Wavenet-D'  => 'James (' . __('Male') . ')',
        'en-GB-Wavenet-F'  => 'Sophie (' . __('Female') . ')',
        // "en-US-Neural2-A" => "Neural2 - MALE",
        // "en-US-Neural2-C" => "Neural2 - FEMALE",
        // "en-US-Neural2-D" => "Neural2 - MALE",
        // "en-US-Neural2-E" => "Neural2 - FEMALE",
        // "en-US-Neural2-F" => "Neural2 - FEMALE",
        // "en-US-Neural2-G" => "Neural2 - FEMALE",
        // "en-US-Neural2-H" => "Neural2 - FEMALE",
        // "en-US-Neural2-I" => "Neural2 - MALE",
        // "en-US-Neural2-J" => "Neural2 - MALE",
        'en-US-News-K'      => 'Lily (' . __('Female') . ')',
        'en-US-News-L'      => 'Olivia (' . __('Female') . ')',
        'en-US-News-M'      => 'Noah (' . __('Male') . ')',
        'en-US-News-N'      => 'Oliver (' . __('Male') . ')',
        'en-US-Polyglot-1'  => 'John (' . __('Male') . ')',
        'en-US-Standard-A'  => 'Michael (' . __('Male') . ')',
        'en-US-Standard-B'  => 'David (' . __('Male') . ')',
        'en-US-Standard-C'  => 'Emma (' . __('Female') . ')',
        'en-US-Standard-D'  => 'William (' . __('Male') . ')',
        'en-US-Standard-E'  => 'Ava (' . __('Female') . ')',
        'en-US-Standard-F'  => 'Sophia (' . __('Female') . ')',
        'en-US-Standard-G'  => 'Isabella (' . __('Female') . ')',
        'en-US-Standard-H'  => 'Charlotte (' . __('Female') . ')',
        'en-US-Standard-I'  => 'James (' . __('Male') . ')',
        'en-US-Standard-J'  => 'Lucas (' . __('Male') . ')',
        'en-US-Studio-M'    => 'Benjamin (' . __('Male') . ')',
        'en-US-Studio-O'    => 'Eleanor (' . __('Female') . ')',
        'en-US-Wavenet-A'   => 'Alexander (' . __('Male') . ')',
        'en-US-Wavenet-B'   => 'Benjamin (' . __('Male') . ')',
        'en-US-Wavenet-C'   => 'Emily (' . __('Female') . ')',
        'en-US-Wavenet-D'   => 'James (' . __('Male') . ')',
        'en-US-Wavenet-E'   => 'Ava (' . __('Female') . ')',
        'en-US-Wavenet-F'   => 'Sophia (' . __('Female') . ')',
        'en-US-Wavenet-G'   => 'Isabella (' . __('Female') . ')',
        'en-US-Wavenet-H'   => 'Charlotte (' . __('Female') . ')',
        'en-US-Wavenet-I'   => 'Alexander (' . __('Male') . ')',
        'en-US-Wavenet-J'   => 'Lucas (' . __('Male') . ')',
        'fil-PH-Standard-A' => 'Maria (' . __('Female') . ')',
        'fil-PH-Standard-B' => 'Juana (' . __('Female') . ')',
        'fil-PH-Standard-C' => 'Juan (' . __('Male') . ')',
        'fil-PH-Standard-D' => 'Pedro (' . __('Male') . ')',
        'fil-PH-Wavenet-A'  => 'Maria (' . __('Female') . ')',
        'fil-PH-Wavenet-B'  => 'Juana (' . __('Female') . ')',
        'fil-PH-Wavenet-C'  => 'Juan (' . __('Male') . ')',
        'fil-PH-Wavenet-D'  => 'Pedro (' . __('Male') . ')',
        // "fil-ph-Neural2-A" => "Neural2 - FEMALE",
        // "fil-ph-Neural2-D" => "Neural2 - MALE",
        'fi-FI-Standard-A' => 'Sofia (' . __('Female') . ')',
        'fi-FI-Wavenet-A'  => 'Sofianna (' . __('Female') . ')',
        // "fr-CA-Neural2-A" => "Neural2 - FEMALE",
        // "fr-CA-Neural2-B" => "Neural2 - MALE",
        // "fr-CA-Neural2-C" => "Neural2 - FEMALE",
        // "fr-CA-Neural2-D" => "Neural2 - MALE",
        'fr-CA-Standard-A' => 'Emma (' . __('Female') . ')',
        'fr-CA-Standard-B' => 'Jean (' . __('Male') . ')',
        'fr-CA-Standard-C' => 'Gabrielle (' . __('Female') . ')',
        'fr-CA-Standard-D' => 'Thomas (' . __('Male') . ')',
        'fr-CA-Wavenet-A'  => 'Amelie (' . __('Female') . ')',
        'fr-CA-Wavenet-B'  => 'Antoine (' . __('Male') . ')',
        'fr-CA-Wavenet-C'  => 'Gabrielle (' . __('Female') . ')',
        'fr-CA-Wavenet-D'  => 'Thomas (' . __('Male') . ')',
        // "fr-FR-Neural2-A" => "Neural2 - FEMALE",
        // "fr-FR-Neural2-B" => "Neural2 - MALE",
        // "fr-FR-Neural2-C" => "Neural2 - FEMALE",
        // "fr-FR-Neural2-D" => "Neural2 - MALE",
        // "fr-FR-Neural2-E" => "Neural2 - FEMALE",
        'fr-FR-Polyglot-1' => 'Jean (' . __('Male') . ')',
        'fr-FR-Standard-A' => 'Marie (' . __('Female') . ')',
        'fr-FR-Standard-B' => 'Pierre (' . __('Male') . ')',
        'fr-FR-Standard-C' => 'Sophie (' . __('Female') . ')',
        'fr-FR-Standard-D' => 'Paul (' . __('Male') . ')',
        'fr-FR-Standard-E' => 'Julie (' . __('Female') . ')',
        'fr-FR-Wavenet-A'  => 'Elise (' . __('Female') . ')',
        'fr-FR-Wavenet-B'  => 'Nicolas (' . __('Male') . ')',
        'fr-FR-Wavenet-C'  => 'Clara (' . __('Female') . ')',
        'fr-FR-Wavenet-D'  => 'Antoine (' . __('Male') . ')',
        'fr-FR-Wavenet-E'  => 'Amelie (' . __('Female') . ')',
        'gl-ES-Standard-A' => 'Ana (' . __('Female') . ')',
        // "de-DE-Neural2-B" => "Neural2 - MALE",
        // "de-DE-Neural2-C" => "Neural2 - FEMALE",
        // "de-DE-Neural2-D" => "Neural2 - MALE",
        // "de-DE-Neural2-F" => "Neural2 - FEMALE",
        'de-DE-Polyglot-1' => 'Johannes (' . __('Male') . ')',
        'de-DE-Standard-A' => 'Anna (' . __('Female') . ')',
        'de-DE-Standard-B' => 'Max (' . __('Male') . ')',
        'de-DE-Standard-C' => 'Sophia (' . __('Female') . ')',
        'de-DE-Standard-D' => 'Paul (' . __('Male') . ')',
        'de-DE-Standard-E' => 'Erik (' . __('Male') . ')',
        'de-DE-Standard-F' => 'Lina (' . __('Female') . ')',
        'de-DE-Wavenet-A'  => 'Eva (' . __('Female') . ')',
        'de-DE-Wavenet-B'  => 'Felix (' . __('Male') . ')',
        'de-DE-Wavenet-C'  => 'Emma (' . __('Female') . ')',
        'de-DE-Wavenet-D'  => 'Lukas (' . __('Male') . ')',
        'de-DE-Wavenet-E'  => 'Nico (' . __('Male') . ')',
        'de-DE-Wavenet-F'  => 'Mia (' . __('Female') . ')',
        'el-GR-Standard-A' => 'Ελένη (' . __('Female') . ')',
        'el-GR-Wavenet-A'  => 'Ελένη (' . __('Female') . ')',
        'gu-IN-Standard-A' => 'દિવ્યા (' . __('Female') . ')',
        'gu-IN-Standard-B' => 'કિશોર (' . __('Male') . ')',
        'gu-IN-Wavenet-A'  => 'દિવ્યા (' . __('Female') . ')',
        'gu-IN-Wavenet-B'  => 'કિશોર (' . __('Male') . ')',
        'he-IL-Standard-A' => 'Tamar (' . __('Female') . ')',
        'he-IL-Standard-B' => 'David (' . __('Male') . ')',
        'he-IL-Standard-C' => 'Michal (' . __('Female') . ')',
        'he-IL-Standard-D' => 'Jonathan (' . __('Male') . ')',
        'he-IL-Wavenet-A'  => 'Yael (' . __('Female') . ')',
        'he-IL-Wavenet-B'  => 'Eli (' . __('Male') . ')',
        'he-IL-Wavenet-C'  => 'Abigail (' . __('Female') . ')',
        'he-IL-Wavenet-D'  => 'Alex (' . __('Male') . ')',
        // "hi-IN-Neural2-A" => "Neural2 - FEMALE",
        // "hi-IN-Neural2-B" => "Neural2 - MALE",
        // "hi-IN-Neural2-C" => "Neural2 - MALE",
        // "hi-IN-Neural2-D" => "Neural2 - FEMALE",
        'hi-IN-Standard-A' => 'Aditi (' . __('Female') . ')',
        'hi-IN-Standard-B' => 'Abhishek (' . __('Male') . ')',
        'hi-IN-Standard-C' => 'Aditya (' . __('Male') . ')',
        'hi-IN-Standard-D' => 'Anjali (' . __('Female') . ')',
        'hi-IN-Wavenet-A'  => 'Kiara (' . __('Female') . ')',
        'hi-IN-Wavenet-B'  => 'Rohan (' . __('Male') . ')',
        'hi-IN-Wavenet-C'  => 'Rishabh (' . __('Male') . ')',
        'hi-IN-Wavenet-D'  => 'Srishti (' . __('Female') . ')',
        'hu-HU-Standard-A' => 'Eszter (' . __('Female') . ')',
        'hu-HU-Wavenet-A'  => 'Lilla (' . __('Female') . ')',
        'is-IS-Standard-A' => 'Guðrún (' . __('Female') . ')',
        'id-ID-Standard-A' => 'Amelia (' . __('Female') . ')',
        'id-ID-Standard-B' => 'Fajar (' . __('Male') . ')',
        'id-ID-Standard-C' => 'Galih (' . __('Male') . ')',
        'id-ID-Standard-D' => 'Kiara (' . __('Female') . ')',
        'id-ID-Wavenet-A'  => 'Nadia (' . __('Female') . ')',
        'id-ID-Wavenet-B'  => 'Reza (' . __('Male') . ')',
        'id-ID-Wavenet-C'  => 'Satria (' . __('Male') . ')',
        'id-ID-Wavenet-D'  => 'Vania (' . __('Female') . ')',
        // "it-IT-Neural2-A" => "Neural2 - FEMALE",
        // "it-IT-Neural2-C" => "Neural2 - MALE",
        'it-IT-Standard-A' => 'Chiara (' . __('Female') . ')',
        'it-IT-Standard-B' => 'Elisa (' . __('Female') . ')',
        'it-IT-Standard-C' => 'Matteo (' . __('Male') . ')',
        'it-IT-Standard-D' => 'Riccardo (' . __('Male') . ')',
        'it-IT-Wavenet-A'  => 'Valentina (' . __('Female') . ')',
        'it-IT-Wavenet-B'  => 'Vittoria (' . __('Female') . ')',
        'it-IT-Wavenet-C'  => 'Andrea (' . __('Male') . ')',
        'it-IT-Wavenet-D'  => 'Luca (' . __('Male') . ')',
        // "ja-JP-Neural2-B" => "Neural2 - FEMALE",
        // "ja-JP-Neural2-C" => "Neural2 - MALE",
        // "ja-JP-Neural2-D" => "Neural2 - MALE",
        'ja-JP-Standard-A' => 'Akane (' . __('Female') . ')',
        'ja-JP-Standard-B' => 'Emi (' . __('Female') . ')',
        'ja-JP-Standard-C' => 'Daisuke (' . __('Male') . ')',
        'ja-JP-Standard-D' => 'Kento (' . __('Male') . ')',
        'ja-JP-Wavenet-A'  => 'Haruka (' . __('Female') . ')',
        'ja-JP-Wavenet-B'  => 'Rin (' . __('Female') . ')',
        'ja-JP-Wavenet-C'  => 'Shun (' . __('Male') . ')',
        'ja-JP-Wavenet-D'  => 'Yuta (' . __('Male') . ')',
        'kn-IN-Standard-A' => 'Dhanya (' . __('Female') . ')',
        'kn-IN-Standard-B' => 'Keerthi (' . __('Male') . ')',
        'kn-IN-Wavenet-A'  => 'Meena (' . __('Female') . ')',
        'kn-IN-Wavenet-B'  => 'Nandini (' . __('Male') . ')',
        // "ko-KR-Neural2-A" => "Neural2 - FEMALE",
        // "ko-KR-Neural2-B" => "Neural2 - FEMALE",
        // "ko-KR-Neural2-C" => "Neural2 - MALE",
        'ko-KR-Standard-A'  => 'So-young (' . __('Female') . ')',
        'ko-KR-Standard-B'  => 'Se-yeon (' . __('Female') . ')',
        'ko-KR-Standard-C'  => 'Min-soo (' . __('Male') . ')',
        'ko-KR-Standard-D'  => 'Seung-woo (' . __('Male') . ')',
        'ko-KR-Wavenet-A'   => 'Ji-soo (' . __('Female') . ')',
        'ko-KR-Wavenet-B'   => 'Yoon-a (' . __('Female') . ')',
        'ko-KR-Wavenet-C'   => 'Tae-hyun (' . __('Male') . ')',
        'ko-KR-Wavenet-D'   => 'Jun-ho (' . __('Male') . ')',
        'lv-LV-Standard-A'  => 'Raivis (' . __('Male') . ')',
        'lv-LT-Standard-A'  => 'Raivis (' . __('Male') . ')',
        'ms-MY-Standard-A'  => 'Amira (' . __('Female') . ')',
        'ms-MY-Standard-B'  => 'Danial (' . __('Male') . ')',
        'ms-MY-Standard-C'  => 'Eira (' . __('Female') . ')',
        'ms-MY-Standard-D'  => 'Farhan (' . __('Male') . ')',
        'ms-MY-Wavenet-A'   => 'Hana (' . __('Female') . ')',
        'ms-MY-Wavenet-B'   => 'Irfan (' . __('Male') . ')',
        'ms-MY-Wavenet-C'   => 'Janna (' . __('Female') . ')',
        'ms-MY-Wavenet-D'   => 'Khairul (' . __('Male') . ')',
        'ml-IN-Standard-A'  => 'Aishwarya (' . __('Female') . ')',
        'ml-IN-Standard-B'  => 'Dhruv (' . __('Male') . ')',
        'ml-IN-Wavenet-A'   => 'Deepthi (' . __('Female') . ')',
        'ml-IN-Wavenet-B'   => 'Gautam (' . __('Male') . ')',
        'ml-IN-Wavenet-C'   => 'Isha (' . __('Female') . ')',
        'ml-IN-Wavenet-D'   => 'Kabir (' . __('Male') . ')',
        'cmn-CN-Standard-A' => 'Xiaomei (' . __('Female') . ')',
        'cmn-CN-Standard-B' => 'Lijun (' . __('Male') . ')',
        'cmn-CN-Standard-C' => 'Minghao (' . __('Male') . ')',
        'cmn-CN-Standard-D' => 'Yingying (' . __('Female') . ')',
        'cmn-CN-Wavenet-A'  => 'Shanshan (' . __('Female') . ')',
        'cmn-CN-Wavenet-B'  => 'Chenchen (' . __('Male') . ')',
        'cmn-CN-Wavenet-C'  => 'Jiahao (' . __('Male') . ')',
        'cmn-CN-Wavenet-D'  => 'Yueyu (' . __('Female') . ')',
        'cmn-TW-Standard-A' => 'Jingwen (' . __('Female') . ')',
        'cmn-TW-Standard-B' => 'Jinghao (' . __('Male') . ')',
        'cmn-TW-Standard-C' => 'Tingting (' . __('Female') . ')',
        'cmn-TW-Wavenet-A'  => 'Yunyun (' . __('Female') . ')',
        'cmn-TW-Wavenet-B'  => 'Zhenghao (' . __('Male') . ')',
        'cmn-TW-Wavenet-C'  => 'Yuehan (' . __('Female') . ')',
        'mr-IN-Standard-A'  => 'Anjali (' . __('Female') . ')',
        'mr-IN-Standard-B'  => 'Aditya (' . __('Male') . ')',
        'mr-IN-Standard-C'  => 'Dipti (' . __('Female') . ')',
        'mr-IN-Wavenet-A'   => 'Gauri (' . __('Female') . ')',
        'mr-IN-Wavenet-B'   => 'Harsh (' . __('Male') . ')',
        'mr-IN-Wavenet-C'   => 'Ishita (' . __('Female') . ')',
        'nb-NO-Standard-A'  => 'Ingrid (' . __('Female') . ')',
        'nb-NO-Standard-B'  => 'Jonas (' . __('Male') . ')',
        'nb-NO-Standard-C'  => 'Marit (' . __('Female') . ')',
        'nb-NO-Standard-D'  => 'Olav (' . __('Male') . ')',
        'nb-NO-Standard-E'  => 'Silje (' . __('Female') . ')',
        'nb-NO-Wavenet-A'   => 'Astrid (' . __('Female') . ')',
        'nb-NO-Wavenet-B'   => 'Eirik (' . __('Male') . ')',
        'nb-NO-Wavenet-C'   => 'Inger (' . __('Female') . ')',
        'nb-NO-Wavenet-D'   => 'Kristian (' . __('Male') . ')',
        'nb-NO-Wavenet-E'   => 'Trine (' . __('Female') . ')',
        'pl-PL-Standard-A'  => 'Agata (' . __('Female') . ')',
        'pl-PL-Standard-B'  => 'Bartosz (' . __('Male') . ')',
        'pl-PL-Standard-C'  => 'Kamil (' . __('Male') . ')',
        'pl-PL-Standard-D'  => 'Julia (' . __('Female') . ')',
        'pl-PL-Standard-E'  => 'Magdalena (' . __('Female') . ')',
        'pl-PL-Wavenet-A'   => 'Natalia (' . __('Female') . ')',
        'pl-PL-Wavenet-B'   => 'Paweł (' . __('Male') . ')',
        'pl-PL-Wavenet-C'   => 'Tomasz (' . __('Male') . ')',
        'pl-PL-Wavenet-D'   => 'Zofia (' . __('Female') . ')',
        'pl-PL-Wavenet-E'   => 'Wiktoria (' . __('Female') . ')',
        // "pt-BR-Neural2-A" => "Neural2 - FEMALE",
        // "pt-BR-Neural2-B" => "Neural2 - MALE",
        // "pt-BR-Neural2-C" => "Neural2 - FEMALE",
        'pt-BR-Standard-A' => 'Ana (' . __('Female') . ')',
        'pt-BR-Standard-B' => 'Carlos (' . __('Male') . ')',
        'pt-BR-Standard-C' => 'Maria (' . __('Female') . ')',
        'pt-BR-Wavenet-A'  => 'Julia (' . __('Female') . ')',
        'pt-BR-Wavenet-B'  => 'João (' . __('Male') . ')',
        'pt-BR-Wavenet-C'  => 'Fernanda (' . __('Female') . ')',
        'pt-PT-Standard-A' => 'Maria (' . __('Female') . ')',
        'pt-PT-Standard-B' => 'José (' . __('Male') . ')',
        'pt-PT-Standard-C' => 'Luís (' . __('Male') . ')',
        'pt-PT-Standard-D' => 'Ana (' . __('Female') . ')',
        'pt-PT-Wavenet-A'  => 'Catarina (' . __('Female') . ')',
        'pt-PT-Wavenet-B'  => 'Miguel (' . __('Male') . ')',
        'pt-PT-Wavenet-C'  => 'João (' . __('Male') . ')',
        'pt-PT-Wavenet-D'  => 'Marta (' . __('Female') . ')',
        'pa-IN-Standard-A' => 'Harpreet (' . __('Female') . ')',
        'pa-IN-Standard-B' => 'Gurpreet (' . __('Male') . ')',
        'pa-IN-Standard-C' => 'Jasmine (' . __('Female') . ')',
        'pa-IN-Standard-D' => 'Rahul (' . __('Male') . ')',
        'pa-IN-Wavenet-A'  => 'Simran (' . __('Female') . ')',
        'pa-IN-Wavenet-B'  => 'Amardeep (' . __('Male') . ')',
        'pa-IN-Wavenet-C'  => 'Kiran (' . __('Female') . ')',
        'pa-IN-Wavenet-D'  => 'Raj (' . __('Male') . ')',
        'ro-RO-Standard-A' => 'Maria (' . __('Female') . ')',
        'ro-RO-Wavenet-A'  => 'Ioana (' . __('Female') . ')',
        'ru-RU-Standard-A' => 'Anastasia',
        'ru-RU-Standard-B' => 'Alexander',
        'ru-RU-Standard-C' => 'Elizabeth',
        'ru-RU-Standard-D' => 'Michael',
        'ru-RU-Standard-E' => 'Victoria',
        'ru-RU-Wavenet-A'  => 'Daria',
        'ru-RU-Wavenet-B'  => 'Dmitry',
        'ru-RU-Wavenet-C'  => 'Kristina',
        'ru-RU-Wavenet-D'  => 'Ivan',
        'ru-RU-Wavenet-E'  => 'Sophia',
        'sr-RS-Standard-A' => 'Ana',
        'sk-SK-Standard-A' => 'Mária (' . __('Female') . ')',
        'sk-SK-Wavenet-A'  => 'Zuzana (' . __('Female') . ')',
        // "es-ES-Neural2-A" => "Neural2 - FEMALE",
        // "es-ES-Neural2-B" => "Neural2 - MALE",
        // "es-ES-Neural2-C" => "Neural2 - FEMALE",
        // "es-ES-Neural2-D" => "Neural2 - FEMALE",
        // "es-ES-Neural2-E" => "Neural2 - FEMALE",
        // "es-ES-Neural2-F" => "Neural2 - MALE",
        'es-ES-Polyglot-1' => 'Juan (' . __('Male') . ')',
        'es-ES-Standard-A' => 'María (' . __('Female') . ')',
        'es-ES-Standard-B' => 'José (' . __('Male') . ')',
        'es-ES-Standard-C' => 'Ana (' . __('Female') . ')',
        'es-ES-Standard-D' => 'Isabel (' . __('Female') . ')',
        'es-ES-Wavenet-B'  => 'Pedro (' . __('Male') . ')',
        'es-ES-Wavenet-C'  => 'Laura (' . __('Female') . ')',
        'es-ES-Wavenet-D'  => 'Julia (' . __('Female') . ')',
        // "es-US-Neural2-A" => "Neural2 - FEMALE",
        // "es-US-Neural2-B" => "Neural2 - MALE",
        // "es-US-Neural2-C" => "Neural2 - MALE",
        'es-US-News-D'     => 'Diego (' . __('Male') . ')',
        'es-US-News-E'     => 'Eduardo (' . __('Male') . ')',
        'es-US-News-F'     => 'Fátima (' . __('Female') . ')',
        'es-US-News-G'     => 'Gabriela (' . __('Female') . ')',
        'es-US-Polyglot-1' => 'Juan (' . __('Male') . ')',
        'es-US-Standard-A' => 'Ana (' . __('Female') . ')',
        'es-US-Standard-B' => 'José (' . __('Male') . ')',
        'es-US-Standard-C' => 'Carlos (' . __('Male') . ')',
        'es-US-Studio-B'   => 'Miguel (' . __('Male') . ')',
        'es-US-Wavenet-A'  => 'Laura (' . __('Female') . ')',
        'es-US-Wavenet-B'  => 'Pedro (' . __('Male') . ')',
        'es-US-Wavenet-C'  => 'Pablo (' . __('Male') . ')',
        'sv-SE-Standard-A' => 'Ebba (' . __('Female') . ')',
        'sv-SE-Standard-B' => 'Saga (' . __('Female') . ')',
        'sv-SE-Standard-C' => 'Linnea (' . __('Female') . ')',
        'sv-SE-Standard-D' => 'Erik (' . __('Male') . ')',
        'sv-SE-Standard-E' => 'Anton (' . __('Male') . ')',
        'sv-SE-Wavenet-A'  => 'Astrid (' . __('Female') . ')',
        'sv-SE-Wavenet-B'  => 'Elin (' . __('Female') . ')',
        'sv-SE-Wavenet-C'  => 'Oskar (' . __('Male') . ')',
        'sv-SE-Wavenet-D'  => 'Hanna (' . __('Female') . ')',
        'sv-SE-Wavenet-E'  => 'Felix (' . __('Male') . ')',
        'ta-IN-Standard-A' => 'Anjali (' . __('Female') . ')',
        'ta-IN-Standard-B' => 'Karthik (' . __('Male') . ')',
        'ta-IN-Standard-C' => 'Priya (' . __('Female') . ')',
        'ta-IN-Standard-D' => 'Ravi (' . __('Male') . ')',
        'ta-IN-Wavenet-A'  => 'Lakshmi (' . __('Female') . ')',
        'ta-IN-Wavenet-B'  => 'Suresh (' . __('Male') . ')',
        'ta-IN-Wavenet-C'  => 'Uma (' . __('Female') . ')',
        'ta-IN-Wavenet-D'  => 'Venkatesh (' . __('Male') . ')',
        '-IN-Standard-A'   => 'Anjali - (' . __('Female') . ')',
        '-IN-Standard-B'   => 'Karthik - (' . __('Male') . ')',
        // "th-TH-Neural2-C" => "Neural2 - FEMALE",
        'th-TH-Standard-A' => 'Ariya - (' . __('Female') . ')',
        'tr-TR-Standard-A' => 'Ayşe (' . __('Female') . ')',
        'tr-TR-Standard-B' => 'Berk (' . __('Male') . ')',
        'tr-TR-Standard-C' => 'Cansu (' . __('Female') . ')',
        'tr-TR-Standard-D' => 'Deniz (' . __('Female') . ')',
        'tr-TR-Standard-E' => 'Emre (' . __('Male') . ')',
        'tr-TR-Wavenet-A'  => 'Gül (' . __('Female') . ')',
        'tr-TR-Wavenet-B'  => 'Mert (' . __('Male') . ')',
        'tr-TR-Wavenet-C'  => 'Nilay (' . __('Female') . ')',
        'tr-TR-Wavenet-D'  => 'Selin (' . __('Female') . ')',
        'tr-TR-Wavenet-E'  => 'Tolga (' . __('Male') . ')',
        'uk-UA-Standard-A' => 'Anya - (' . __('Female') . ')',
        'uk-UA-Wavenet-A'  => 'Dasha - (' . __('Female') . ')',
        // "vi-VN-Neural2-A" => "Neural2 - FEMALE",
        // "vi-VN-Neural2-D" => "Neural2 - MALE",
        'vi-VN-Standard-A' => 'Mai (' . __('Female') . ')',
        'vi-VN-Standard-B' => 'Nam (' . __('Male') . ')',
        'vi-VN-Standard-C' => 'Hoa (' . __('Female') . ')',
        'vi-VN-Standard-D' => 'Huy (' . __('Male') . ')',
        'vi-VN-Wavenet-A'  => 'Lan (' . __('Female') . ')',
        'vi-VN-Wavenet-B'  => 'Son (' . __('Male') . ')',
        'vi-VN-Wavenet-C'  => 'Thao (' . __('Female') . ')',
        'vi-VN-Wavenet-D'  => 'Tuan (' . __('Male') . ')',
    ];

    return $voiceNames[$hash] ?? $hash;
}

function format_double($number)
{
    $parts = explode('.', $number);

    if (count($parts) == 1) {
        return $parts[0] . '.0';
    }

    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : '';

    if (strlen($decimalPart) > 1) {
        $secondDecimalPart = substr($decimalPart, 1);
    } else {
        $secondDecimalPart = '0';
    }

    return $integerPart . '.' . $decimalPart[0] . '.' . $secondDecimalPart;
}

function currencyShouldDisplayOnRight($currencySymbol)
{
    return in_array($currencySymbol, config('currency.currencies_with_right_symbols'), true);
}

function getMetaTitle($setting, $settingTwo, $ext_title = null)
{
    $ext_title = $ext_title == null ? ' | ' . __('Home') : $ext_title;
    $lang = app()->getLocale();

    if ($lang == $settingTwo->languages_default) {
        if (isset($setting->meta_title)) {
            $title = $setting->meta_title;
        } else {
            $title = $setting->site_name . $ext_title;
        }
    } else {
        $meta_title = PrivacyTerms::where('type', 'meta_title')->where('lang', $lang)->first();
        if ($meta_title) {
            $title = $meta_title->content;
        } else {

            if (isset($setting->meta_title)) {
                $title = $setting->meta_title;
            } else {
                $title = $setting->site_name . $ext_title;
            }
        }
    }

    return $title;
}

function getMetaDesc($setting, $settingTwo)
{
    $lang = app()->getLocale();

    if ($lang == $settingTwo->languages_default) {
        if (isset($setting->meta_description)) {
            $desc = $setting->meta_description;
        } else {
            $desc = '';
        }
    } else {
        $meta_description = PrivacyTerms::where('type', 'meta_desc')->where('lang', $lang)->first();
        if ($meta_description) {
            $desc = $meta_description->content;
        } else {

            if (isset($setting->meta_description)) {
                $desc = $setting->meta_description;
            } else {
                $desc = '';
            }
        }
    }

    return $desc;
}

function parseRSS($feed_url, $limit = 10)
{

    try {
        $rss = simplexml_load_file($feed_url);
        $posts = [];
        $id = 1;

        foreach ($rss->channel->item as $item) {
            if ($id > $limit) {
                break;
            }
            $posts[] = [
                'id'          => $id,
                'link'        => $item->link,
                'title'       => $item->title,
                'description' => $item->description,
                'image'       => $item->enclosure ? $item->enclosure['url'] : null,
            ];
            $id++;
        }

        return $posts;

    } catch (Exception $e) {
        return $e->getMessage();
    }

}

function isChatBot($id)
{

    if (! $id) {
        return false;
    }

    $isChatBot = App\Models\ChatBotHistory::where('user_openai_chat_id', $id)->first();

    if ($isChatBot) {
        return true;
    }

    return false;

}

function ThumbImage($source_file, $max_width = 450, $max_height = 450, $quality = 80)
{
    if (setting('image_thumbnail') == 0 || Str::contains($source_file, 'http') || Str::contains($source_file, 'loading.svg')) {
        return $source_file;
    }

    $source_file = public_path($source_file);
    $disk = 'thumbs';
    $dst_dir = ''; // Since setting the root in the disk configuration, leave this empty
    if (! Storage::disk($disk)->exists($dst_dir)) {
        Storage::disk($disk)->makeDirectory($dst_dir, $mode = 7777, true, true);
    }

    $dst_file_name = basename($source_file);
    $is_url = filter_var($source_file, FILTER_VALIDATE_URL);
    if ($is_url) {
        $url_parts = pathinfo($source_file);
        $extension = strtolower($url_parts['extension']);
        $mime_map = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
        ];
        if (array_key_exists($extension, $mime_map)) {
            $mime = $mime_map[$extension];
        } else {
            $mime = mime_content_type($source_file);
        }
        $dst_file_name .= '.' . $extension;
    } else {
        // check if file exists
        if (! file_exists($source_file)) {
            return $source_file;
        }

        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];
    }
    if (Storage::disk($disk)->exists($dst_file_name)) {
        return Storage::disk($disk)->url($dst_file_name);
    } else {
        if ($is_url) {
            $temp_source_file = tempnam(sys_get_temp_dir(), 'thumb'); // Create temporary file
            file_put_contents($temp_source_file, file_get_contents($source_file)); // Download file
            $source_file = $temp_source_file;
        }
        $dst_dir = Storage::disk($disk)->path($dst_dir . $dst_file_name);
        switch ($mime) {
            case 'image/gif':
                $image_create = 'imagecreatefromgif';
                $image = 'imagegif';

                break;

            case 'image/png':
                $image_create = 'imagecreatefrompng';
                $image = 'imagepng';
                $quality = 7;

                break;

            case 'image/jpeg':
                $image_create = 'imagecreatefromjpeg';
                $image = 'imagejpeg';
                $quality = 80;

                break;
            case 'image/jpg':
                $image_create = 'imagecreatefromjpg';
                $image = 'imagejpg';
                $quality = 80;

                break;

            default:
                // Handle unsupported file types or invalid MIME types
                if ($is_url) {
                    unlink($source_file); // Remove temporary file
                }

                return false;

                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);

        $width = imagesx($src_img);
        $height = imagesy($src_img);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;

        // if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if ($width_new > $width) {
            // cut point by height
            $h_point = (($height - $height_new) / 2);
            // copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        } else {
            // cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
        $image($dst_img, $dst_dir, $quality);

        if ($is_url && file_exists($source_file)) {
            unlink($source_file); // Remove temporary file
        }

        if ($dst_img) {
            imagedestroy($dst_img);
        }
        if ($src_img) {
            imagedestroy($src_img);
        }

        return Storage::disk($disk)->url($dst_file_name);
    }
}

function PurgeThumbImages()
{
    $dst_dir = public_path('uploads/thumbnail/');

    try {
        \Illuminate\Support\Facades\File::deleteDirectory($dst_dir);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
    }
}

function convertHistoryToGemini($history)
{
    $convertedHistory = [];
    $system = false;
    foreach ($history as $item) {
        $role = $item['role'];
        switch ($role) {
            case 'user':
                $role = 'user';

                break;
            case 'assistant':
                $role = 'model';

                break;
            case 'system':
                $system = true;
                $role = 'user';

                break;
            default:
                $role = 'user';

                break;
        }

        $content = $item['content'];

        if (is_string($content)) {
            $convertedItem = [
                'parts' => [
                    [
                        'text' => $content,
                    ],
                ],
                'role' => $role,
            ];
        } elseif (is_array($content)) {
            $isImage = (bool) array_filter($content, function ($item) {
                return isset($item['type']) && $item['type'] === 'image_url';
            });

            if ($isImage) {
                $convertedItem = [
                    'parts' => [],
                ];

                foreach ($content as $item) {

                    if ($item['type'] == 'text') {
                        $convertedItem['parts'][] = [
                            'text' => $item['text'],
                        ];
                    } elseif ($item['type'] == 'image_url') {
                        $convertedItem['parts'][] = [
                            'inline_data' => [
                                'mime_type' => extractMimeType($item['image_url']['url']),
                                'data'      => extractBase64ImageData($item['image_url']['url']),
                            ],
                        ];
                    }

                }

            }
        }

        $convertedHistory[] = $convertedItem;

        if ($system) {
            // add system back message
            $convertedItem = [
                'parts' => [
                    [
                        'text' => 'Yes, sure.',
                    ],
                ],
                'role' => 'model',
            ];
            $convertedHistory[] = $convertedItem;
            $system = false;
        }
    }

    return adjustHistory($convertedHistory);
}

function extractMimeType($data)
{
    $pattern = '/^data:(image\/[a-z]+);base64,/';
    if (preg_match($pattern, $data, $matches)) {
        return $matches[1]; // MIME türünü döndürür
    }

    return null; // Eşleşme bulunamazsa null döndürür
}

function extractBase64ImageData($dataURI)
{
    // JPEG veya PNG için MIME türlerini destekleyen regex deseni
    $pattern = '/^data:image\/(?:jpeg|png);base64,/';
    if (preg_match($pattern, $dataURI, $matches)) {
        // Regex ile başlık bulunursa, başlığı atıp sadece Base64 verisini döndür
        return substr($dataURI, strlen($matches[0]));
    }

    return null; // Uygun başlık bulunamazsa null döndür
}

function adjustHistory($history)
{
    $adjustedHistory = [];

    // Iterate through the history
    foreach ($history as $index => $item) {
        // Add the current item to the adjusted history
        $adjustedHistory[] = $item;

        if (isset($history[$index + 1]['role']) and isset($item['role'])) {
            // Check if the next item exists and has the same role as the current item
            if (isset($history[$index + 1]) && $history[$index + 1]['role'] === $item['role']) {
                // If the next item has the same role, add the opposite role message after it
                $adjustedHistory[] = [
                    'role'  => ($item['role'] === 'user' ? 'model' : 'user'),
                    'parts' => [
                        [
                            'text' => 'This is a placeholder message.',
                        ],
                    ],
                ];
            }
        }

    }

    return $adjustedHistory;
}

if (! function_exists('viewName')) {
    function viewName(string $viewName): string
    {
        return $viewName;
    }
}

function showTeamFunctionality(): bool
{
    $shouldCheck = Helper::appIsDemo();

    if ($shouldCheck) {
        return true;
    }

    $plan = Plan::query()->where('is_team_plan', 1)->first();

    return Helper::setting('team_functionality') && ! auth()?->user()?->getAttribute('team_id') && $plan;
}

function generateRandomWords($wordCount): string
{
    $words = [];
    for ($i = 0; $i < $wordCount; $i++) {
        $words[] = \Illuminate\Support\Str::random(random_int(2, 5));
    }

    return implode(' ', $words);
}

if (! function_exists('isDBDriverSQLite')) {
    function isDBDriverSQLite(): bool
    {
        return DB::connection() instanceof \Illuminate\Database\SQLiteConnection;
    }
}

if (! function_exists('class_interface_exists')) {
    /**
     * @param  string<class-string>  $class
     * @param  string<class-string>  $interface
     */
    function class_interface_exists(string $class, string $interface): bool
    {
        return in_array($interface, class_implements($class), true);
    }
}

if (! function_exists('getClientIp')) {
    function getClientIp(): string
    {
        return request()?->server('HTTP_CF_CONNECTING_IP') ?? request()?->ip();
    }
}

if (! function_exists('number_shorten')) {
    function number_shorten($number, $precision = 3)
    {
        $suffixes = ['', __('K'), __('M'), __('B'), __('T'), __('Qa'), __('Qi')];
        $index = (int) log(abs($number), 1000);
        $index = max(0, min(count($suffixes) - 1, $index));
        $shortNumber = $number / (1000 ** $index);
        $formatted = rtrim(rtrim(number_format($shortNumber, $precision), '0'), '.');

        return $formatted . $suffixes[$index];
    }
}

if (! function_exists('getSocialMediaIcon')) {
    function getSocialMediaIcon(?string $socialMediaName)
    {
        $image = '';

        switch ($socialMediaName) {
            case 'instagram':
                $image =
                    '<svg width="20" height="20" viewBox="0 0 20 20" fill="url(#paint0_linear_2140_988)" xmlns="http://www.w3.org/2000/svg"> <path d="M10.0215 5.03711C12.7285 5.03711 14.9629 7.27148 14.9629 9.97852C14.9629 12.7285 12.7285 14.9199 10.0215 14.9199C7.27148 14.9199 5.08008 12.7285 5.08008 9.97852C5.08008 7.27148 7.27148 5.03711 10.0215 5.03711ZM10.0215 13.2012C11.7832 13.2012 13.2012 11.7832 13.2012 9.97852C13.2012 8.2168 11.7832 6.79883 10.0215 6.79883C8.2168 6.79883 6.79883 8.2168 6.79883 9.97852C6.79883 11.7832 8.25977 13.2012 10.0215 13.2012ZM16.2949 4.86523C16.2949 5.50977 15.7793 6.02539 15.1348 6.02539C14.4902 6.02539 13.9746 5.50977 13.9746 4.86523C13.9746 4.2207 14.4902 3.70508 15.1348 3.70508C15.7793 3.70508 16.2949 4.2207 16.2949 4.86523ZM19.5605 6.02539C19.6465 7.61523 19.6465 12.3848 19.5605 13.9746C19.4746 15.5215 19.1309 16.8535 18.0137 18.0137C16.8965 19.1309 15.5215 19.4746 13.9746 19.5605C12.3848 19.6465 7.61523 19.6465 6.02539 19.5605C4.47852 19.4746 3.14648 19.1309 1.98633 18.0137C0.869141 16.8535 0.525391 15.5215 0.439453 13.9746C0.353516 12.3848 0.353516 7.61523 0.439453 6.02539C0.525391 4.47852 0.869141 3.10352 1.98633 1.98633C3.14648 0.869141 4.47852 0.525391 6.02539 0.439453C7.61523 0.353516 12.3848 0.353516 13.9746 0.439453C15.5215 0.525391 16.8965 0.869141 18.0137 1.98633C19.1309 3.10352 19.4746 4.47852 19.5605 6.02539ZM17.498 15.6504C18.0137 14.4043 17.8848 11.3965 17.8848 9.97852C17.8848 8.60352 18.0137 5.5957 17.498 4.30664C17.1543 3.49023 16.5098 2.80273 15.6934 2.50195C14.4043 1.98633 11.3965 2.11523 10.0215 2.11523C8.60352 2.11523 5.5957 1.98633 4.34961 2.50195C3.49023 2.8457 2.8457 3.49023 2.50195 4.30664C1.98633 5.5957 2.11523 8.60352 2.11523 9.97852C2.11523 11.3965 1.98633 14.4043 2.50195 15.6504C2.8457 16.5098 3.49023 17.1543 4.34961 17.498C5.5957 18.0137 8.60352 17.8848 10.0215 17.8848C11.3965 17.8848 14.4043 18.0137 15.6934 17.498C16.5098 17.1543 17.1973 16.5098 17.498 15.6504Z"/> <defs> <linearGradient id="paint0_linear_2140_988" x1="0.375" y1="10" x2="19.625" y2="10" gradientUnits="userSpaceOnUse"> <stop stop-color="#EB6434"/> <stop offset="0.545" stop-color="#BB2D9F"/> <stop offset="0.98" stop-color="#BB802D"/> </linearGradient> </defs> </svg>';

                break;
            case 'x':
                $image =
                    '<svg width="18" height="18" viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path d="M10.7124 7.62177L17.4133 0H15.8254L10.0071 6.61788L5.35992 0H0L7.02738 10.0074L0 18H1.58799L7.73237 11.0113L12.6401 18H18L10.7121 7.62177H10.7124ZM8.53747 10.0956L7.82546 9.09906L2.16017 1.16971H4.59922L9.17118 7.56895L9.8832 8.56546L15.8262 16.8835H13.3871L8.53747 10.096V10.0956Z"/> </svg>';

                break;
            case 'linkedin':
                $image =
                    '<svg width="16" height="16" viewBox="0 0 16 16" fill="#0077B5" xmlns="http://www.w3.org/2000/svg"><path d="M3.88281 15.4209H0.794922V5.49316H3.88281V15.4209ZM2.32227 4.16504C1.35938 4.16504 0.5625 3.33496 0.5625 2.33887C0.5625 1.37598 1.35938 0.579102 2.32227 0.579102C3.31836 0.579102 4.11523 1.37598 4.11523 2.33887C4.11523 3.33496 3.31836 4.16504 2.32227 4.16504ZM12.3496 15.4209V10.6064C12.3496 9.44434 12.3164 7.9834 10.7227 7.9834C9.12891 7.9834 8.89648 9.21191 8.89648 10.5068V15.4209H5.80859V5.49316H8.76367V6.85449H8.79688C9.22852 6.09082 10.2246 5.26074 11.7188 5.26074C14.8398 5.26074 15.4375 7.31934 15.4375 9.97559V15.4209H12.3496Z"/></svg>';

                break;
            case 'facebook':
                $image =
                    '<svg width="10" height="18" viewBox="0 0 10 18" fill="#1877F2" xmlns="http://www.w3.org/2000/svg"><path d="M8.96777 10.0625H6.47754V17.5H3.15723V10.0625H0.43457V7.00781H3.15723V4.65039C3.15723 1.99414 4.75098 0.5 7.1748 0.5C8.33691 0.5 9.56543 0.732422 9.56543 0.732422V3.35547H8.2041C6.87598 3.35547 6.47754 4.15234 6.47754 5.01562V7.00781H9.43262L8.96777 10.0625Z"/></svg>';

                break;
            default:
                $image =
                    '<svg width="20" height="20" viewBox="0 0 20 20" fill="url(#paint0_linear_2140_988)" xmlns="http://www.w3.org/2000/svg"> <path d="M10.0215 5.03711C12.7285 5.03711 14.9629 7.27148 14.9629 9.97852C14.9629 12.7285 12.7285 14.9199 10.0215 14.9199C7.27148 14.9199 5.08008 12.7285 5.08008 9.97852C5.08008 7.27148 7.27148 5.03711 10.0215 5.03711ZM10.0215 13.2012C11.7832 13.2012 13.2012 11.7832 13.2012 9.97852C13.2012 8.2168 11.7832 6.79883 10.0215 6.79883C8.2168 6.79883 6.79883 8.2168 6.79883 9.97852C6.79883 11.7832 8.25977 13.2012 10.0215 13.2012ZM16.2949 4.86523C16.2949 5.50977 15.7793 6.02539 15.1348 6.02539C14.4902 6.02539 13.9746 5.50977 13.9746 4.86523C13.9746 4.2207 14.4902 3.70508 15.1348 3.70508C15.7793 3.70508 16.2949 4.2207 16.2949 4.86523ZM19.5605 6.02539C19.6465 7.61523 19.6465 12.3848 19.5605 13.9746C19.4746 15.5215 19.1309 16.8535 18.0137 18.0137C16.8965 19.1309 15.5215 19.4746 13.9746 19.5605C12.3848 19.6465 7.61523 19.6465 6.02539 19.5605C4.47852 19.4746 3.14648 19.1309 1.98633 18.0137C0.869141 16.8535 0.525391 15.5215 0.439453 13.9746C0.353516 12.3848 0.353516 7.61523 0.439453 6.02539C0.525391 4.47852 0.869141 3.10352 1.98633 1.98633C3.14648 0.869141 4.47852 0.525391 6.02539 0.439453C7.61523 0.353516 12.3848 0.353516 13.9746 0.439453C15.5215 0.525391 16.8965 0.869141 18.0137 1.98633C19.1309 3.10352 19.4746 4.47852 19.5605 6.02539ZM17.498 15.6504C18.0137 14.4043 17.8848 11.3965 17.8848 9.97852C17.8848 8.60352 18.0137 5.5957 17.498 4.30664C17.1543 3.49023 16.5098 2.80273 15.6934 2.50195C14.4043 1.98633 11.3965 2.11523 10.0215 2.11523C8.60352 2.11523 5.5957 1.98633 4.34961 2.50195C3.49023 2.8457 2.8457 3.49023 2.50195 4.30664C1.98633 5.5957 2.11523 8.60352 2.11523 9.97852C2.11523 11.3965 1.98633 14.4043 2.50195 15.6504C2.8457 16.5098 3.49023 17.1543 4.34961 17.498C5.5957 18.0137 8.60352 17.8848 10.0215 17.8848C11.3965 17.8848 14.4043 18.0137 15.6934 17.498C16.5098 17.1543 17.1973 16.5098 17.498 15.6504Z"/> <defs> <linearGradient id="paint0_linear_2140_988" x1="0.375" y1="10" x2="19.625" y2="10" gradientUnits="userSpaceOnUse"> <stop stop-color="#EB6434"/> <stop offset="0.545" stop-color="#BB2D9F"/> <stop offset="0.98" stop-color="#BB802D"/> </linearGradient> </defs> </svg>';

                break;
        }

        return $image;
    }
}
