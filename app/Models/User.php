<?php

namespace App\Models;

use App\Enums\Plan\FrequencyEnum;
use App\Enums\Roles;
use App\Helpers\Classes\Helper;
use App\Models\Chatbot\Chatbot;
use App\Models\Concerns\User\HasCredit;
use App\Models\Integration\UserIntegration;
use App\Models\Team\Team;
use App\Models\Team\TeamMember;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription as Subscriptions;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Billable;
    use HasApiTokens;
    use HasCredit;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'coingate_subscriber_id',
        'team_id',
        'team_manager_id',
        'name',
        'surname',
        'email',
        'country',
        'otp',
        'type',
        'password',
        'affiliate_id',
        'affiliate_code',
        'email_confirmation_code',
        'email_confirmed',
        'password_reset_code',
        'anthropic_api_keys',
        'api_keys',
        'defi_setting',
        'affiliate_status',
        'entity_credits',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'defi_setting',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'defi_setting'      => 'json',
        'type'              => Roles::class,
        'entity_credits'    => 'array',
    ];

    public function teamId()
    {
        return $this->team_id;
    }

    public function checkPermission(string $key): bool
    {
        if ($this->type === Roles::SUPER_ADMIN) {
            return true;
        }

        if (Helper::adminPermissions($key) && $this->type === Roles::ADMIN) {
            return true;
        }

        return false;
    }

    public function isConfirmed(): bool
    {
        return $this->email_confirmed;
    }

    public function isAdmin(): bool
    {
        return in_array($this->type, [Roles::SUPER_ADMIN, Roles::ADMIN], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->type === Roles::SUPER_ADMIN;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(static function ($user) {
            $user->orders()->delete();
        });

        static::deleted(static function ($user) {
            $user->orders()->delete();
        });
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(UserIntegration::class)->with('integration');
    }

    public function isUser(): bool
    {
        return $this->type === Roles::USER;
    }

    public function teamManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_manager_id', 'id');
    }

    public function teamMember(): HasOne
    {
        return $this->hasOne(TeamMember::class, 'user_id', 'id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function myCreatedTeam()
    {
        return $this->hasOne(Team::class, 'user_id', 'id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function relationPlan()
    {
        return $this->hasOneThrough(
            Plan::class,
            Subscriptions::class,
            'user_id',
            'id',
            'id',
            'plan_id'
        )->whereIn('stripe_status', [
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
        ]);
    }

    public function fullName(): string
    {
        return $this->name . ' ' . $this->surname;
    }

    public function email()
    {
        return $this->email;
    }

    public function openai()
    {
        return $this->hasMany(UserOpenai::class);
    }

    public function orders()
    {
        return $this->hasMany(UserOrder::class)->orderBy('created_at', 'desc');
    }

    public function plan()
    {
        return $this->hasMany(UserOrder::class)
            ->where('type', 'subscription')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function activePlan()
    {
        // $activeSub = $this->subscriptions()->where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->first();
        // $userId=Auth::user()->id;
        $userId = $this->id;
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            $plan = Plan::where('id', $activeSub->plan_id)->first();
            if (is_null($plan)) {
                return null;
            }
            $difference = $activeSub->updated_at->diffInDays(Carbon::now());
            if ($plan->frequency === FrequencyEnum::MONTHLY->value) {
                if ($difference < 31) {
                    return $plan;
                }
            } elseif ($plan->frequency === FrequencyEnum::YEARLY->value) {
                if ($difference < 365) {
                    return $plan;
                }
            } else {
                return $plan;
            }
        } else {
            $activeSub = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSub != null) {
                $plan = Plan::where('id', $activeSub->plan_id)->first();
                if (is_null($plan)) {
                    return null;
                }
                $difference = $activeSub->updated_at->diffInDays(Carbon::now());
                if ($plan->frequency == FrequencyEnum::MONTHLY->value || $plan->frequency == FrequencyEnum::LIFETIME_MONTHLY->value) {
                    if ($difference < 31) {
                        return $plan;
                    }
                } elseif ($plan->frequency == FrequencyEnum::YEARLY->value || $plan->frequency == FrequencyEnum::LIFETIME_YEARLY->value) {
                    if ($difference < 365) {
                        return $plan;
                    }
                } else {
                    return $plan;
                }
            } else {
                return null;
            }
        }
    }

    // Support Requests
    public function supportRequests()
    {
        return $this->hasMany(UserSupport::class);
    }

    // Favorites
    public function favoriteOpenai()
    {
        return $this->belongsToMany(OpenAIGenerator::class, 'user_favorites', 'user_id', 'openai_id');
    }

    // Affiliate
    public function affiliates()
    {
        return $this->hasMany(User::class, 'affiliate_id', 'id');
    }

    public function affiliateOf()
    {
        return $this->belongsTo(User::class, 'affiliate_id', 'id');
    }

    public function withdrawals()
    {
        return $this->hasMany(UserAffiliate::class);
    }

    // Chat
    public function openaiChat()
    {
        return $this->hasMany(UserOpenaiChat::class);
    }

    // Avatar
    public function getAvatar()
    {
        if ($this->avatar == null) {
            return '<span class="avatar">' . Str::upper(substr($this->name, 0, 1)) . Str::upper(substr($this->surname, 0, 1)) . '</span>';
        } else {
            $avatar = $this->avatar;
            if (strpos($avatar, 'http') === false || strpos($avatar, 'https') === false) {
                $avatar = '/' . $avatar;
            }

            return ' <span class="avatar" style="background-image: url(' . custom_theme_url($avatar) . ')"></span>';
        }
    }

    public function couponsUsed()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_users')
            ->withTimestamps();
    }

    public function twitterSettings()
    {
        if (class_exists(\App\Extensions\AISocialMedia\System\Models\TwitterSettings::class)) {
            return $this->hasMany(\App\Extensions\AISocialMedia\System\Models\TwitterSettings::class);
        }

        return null;
    }

    public function linkedinSettings()
    {
        if (class_exists(\App\Extensions\AISocialMedia\System\Models\LinkedinTokens::class)) {
            return $this->hasMany(\App\Extensions\AISocialMedia\System\Models\LinkedinTokens::class);
        }

        return null;
    }

    public function scheduledPosts()
    {
        if (class_exists(\App\Extensions\AISocialMedia\System\Models\ScheduledPost::class)) {
            return $this->hasMany(\App\Extensions\AISocialMedia\System\Models\ScheduledPost::class);
        }

        return null;
    }

    public function folders()
    {
        return $this->hasMany(Folders::class, 'created_by');
    }

    // my companies
    public function companies()
    {
        return $this->hasMany(Company::class, 'user_id');
    }

    public function getCompanies()
    {
        return $this->companies()->orderBy('name', 'asc')->get();
    }

    public function scopeAdmins(Builder $query): void
    {
        $query->where('type', 'admin');
    }

    public function chatbots(): HasMany
    {
        return $this->hasMany(Chatbot::class, 'user_id');
    }

    public function externalChatbots(): HasMany
    {
        return $this->hasMany(\App\Extensions\Chatbot\System\Models\Chatbot::class, 'user_id');
    }
}
