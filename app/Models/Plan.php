<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Enums\Plan\FrequencyEnum;
use App\Enums\Plan\PlanType;
use App\Enums\Plan\TypeEnum;
use App\Services\Common\MenuService;
use App\Services\Finance\PlanService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Yediyuz\Helpers\ArrayHelper;

class Plan extends Model
{
    use HasFactory;

    private ?array $mergedAiFeatures = null;

    private function getMergedAiFeatures(): array
    {
        if ($this->mergedAiFeatures === null) {
            $this->mergedAiFeatures = array_merge(
                (array) $this->open_ai_items,
                (array) $this->plan_ai_tools,
                (array) $this->plan_features,
            );
        }

        return $this->mergedAiFeatures;
    }

    protected $fillable = [
        'active',
        'name',
        'price',
        'currency',
        'frequency',
        'is_featured',
        'stripe_product_id',
        'ai_name',
        'max_tokens',
        'can_create_ai_images',
        'plan_type',
        'features',
        'type',
        'user_api',
        'is_team_plan',
        'plan_allow_seat',
        'trial_days',
        'open_ai_items',
        'description',
        'plan_ai_tools',
        'plan_features',
        'reset_credits_on_renewal',
        'default_ai_model',
        'ai_models',
        'hidden',
        'max_subscribe',
        'last_date',
    ];

    protected $attributes = [
        'currency'             => 'USD',
        'ai_name'              => 'AI',
        'max_tokens'           => null,
        'can_create_ai_images' => true,
        'type'                 => 'subscription',
    ];

    protected $casts = [
        'open_ai_items'            => 'json',
        'plan_ai_tools'            => 'json',
        'plan_features'            => 'json',
        'ai_models'                => 'array',
        'ai_models.*.credit'       => 'float',
        'ai_models.*.isUnlimited'  => 'boolean',
        'is_featured'              => 'boolean',
        'is_team_plan'             => 'boolean',
        'active'                   => 'boolean',
        'user_api'                 => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saved(static function () {
            PlanService::clearCache();
        });

        static::deleted(static function () {
            PlanService::clearCache();
        });
    }

    protected function userApi(): Attribute
    {
        return Attribute::make(
            get: static fn (?int $value) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            set: static fn (?bool $value) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
        );
    }

    protected function planAiTools(): Attribute
    {
        $getPlanAiTools = static function (?string $value) {
            return json_decode(empty($value) ? '[]' : $value, true, 512, JSON_THROW_ON_ERROR) ?? [];
        };

        $setPlanAiTools = static function (?array $value) {
            return json_encode(empty($value) ? [] : $value, JSON_THROW_ON_ERROR);
        };

        return Attribute::make(
            get: static fn (?string $value) => $getPlanAiTools($value),
            set: static fn (?array $value) => $setPlanAiTools($value),
        );
    }

    protected function planFeatures(): Attribute
    {
        $getPlanFeatures = static function (?string $value) {
            return json_decode(empty($value) ? '[]' : $value, true, 512, JSON_THROW_ON_ERROR) ?? [];
        };

        $setPlanFeatures = static function (?array $value) {
            return json_encode(empty($value) ? [] : $value, JSON_THROW_ON_ERROR);
        };

        return Attribute::make(
            get: static fn (?string $value) => $getPlanFeatures($value),
            set: static fn (?array $value) => $setPlanFeatures($value),
        );
    }

    protected function openAiItems(): Attribute
    {
        $getOpenAiItems = static function (?string $value) {
            $decoded = json_decode(empty($value) ? '[]' : $value, true, 512, JSON_THROW_ON_ERROR) ?? [];
            if (is_array($decoded) && isset($decoded[0])) {
                return array_fill_keys($decoded, true);
            }

            return $decoded;
        };

        $setOpenAiItems = static function (?array $value) {
            return json_encode(empty($value) ? [] : $value, JSON_THROW_ON_ERROR);
        };

        return Attribute::make(
            get: static fn (?string $value) => $getOpenAiItems($value),
            set: static fn (?array $value) => $setOpenAiItems($value),
        );
    }

    public function getCredit(string $engineKey, string $entityKey): array
    {
        return $this->ai_models[$engineKey][$entityKey] ?? [
            'credit'      => 0,
            'isUnlimited' => false,
        ];
    }

    protected function aiModels(): Attribute
    {
        $getAiEngines = static function (?string $value) {
            $nestedPlanLimits = EngineEnum::getNestedPlanLimits();
            if (! is_null($value)) {
                $aiEngines = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                $aiEngines = Arr::wrap($aiEngines);
                $aiEngines = ArrayHelper::replace($nestedPlanLimits, $aiEngines);
            } else {
                $aiEngines = $nestedPlanLimits;
            }

            return collect($aiEngines)->map(function ($aiModels) {
                return Arr::mapWithKeys($aiModels, static function ($limit, $key) {
                    $aiModel = EntityEnum::fromSlug($key);

                    return [$aiModel->slug() => $limit];
                });
            })->toArray();
        };

        $setAiEngines = static function (?array $value) {
            if (! $value) {
                return null;
            }
            $value = ArrayHelper::replace(EngineEnum::getNestedPlanLimits(), $value);

            return collect($value)->map(function ($aiModels) {
                return Arr::mapWithKeys($aiModels, static function ($limit, $key) {
                    if (isset($limit['credit'])) {
                        $limit['credit'] = (float) $limit['credit'];  // Cast credit to float
                    }

                    return [$key => $limit];
                });
            })->toJson();
        };

        return Attribute::make(
            get: static fn (?string $value) => $getAiEngines($value),
            set: static fn (?array $value) => $setAiEngines($value),
        );
    }

    protected function isTeamPlan(): Attribute
    {
        $getIsTeamPlan = static function (?string $value) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        };

        $setIsTeamPlan = static function (?bool $value) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        };

        return Attribute::make(
            get: static fn (?string $value) => $getIsTeamPlan($value),
            set: static fn (?bool $value) => $setIsTeamPlan($value),
        );
    }

    public function gatewayProducts(): HasMany
    {
        return $this->hasMany(GatewayProducts::class, 'plan_id', 'id');
    }

    // revenuecat_products
    public function revenuecat_products()
    {
        return $this->hasMany(RevenueCatProducts::class, 'plan_id', 'id');
    }

    public function checkOpenAiItemCount(): int
    {
        $features = $this->getMergedAiFeatures();

        $activeFeatures = array_filter($features, static function ($value) {
            return $value === true;
        });

        return count($activeFeatures);
    }

    public function checkOpenAiItem($key): bool
    {
        $features = $this->getMergedAiFeatures();

        $deprecatedKeysWithValues = [
            'ai_webchat'                  => 'ai_web_chat_extension',
            'ai_article_wizard_generator' => 'ai_article_wizard',
            'ai_content_detect'           => 'ai_detector_extension',
            'ai_plagiarism'               => 'ai_plagiarism_extension',
            'ai_social_media'             => 'ai_social_media_extension',
        ];

        $key2 = $key;
        if (array_key_exists($key, $deprecatedKeysWithValues)) {
            $key2 = $deprecatedKeysWithValues[$key];
        }

        return (isset($features[$key]) && $features[$key] === true) || (isset($features[$key2]) && $features[$key2] === true);
    }

    public function isFree(): bool
    {
        return $this->price === 0;
    }

    public static function openAiGeneratorsQuery(): Builder
    {
        return once(
            static fn () => OpenAIGenerator::select(['slug', 'title', 'filters', 'active'])
                ->where('active', true)
                ->where('slug', 'not like', 'ai_%')
                ->whereNotNull('filters')
                ->where('filters', '!=', '')
        );
    }

    public static function getFreshOpenAiItems(string|array $values): array
    {
        $values = Arr::wrap($values);

        if ($values === []) {
            return self::openAiGeneratorsQuery()->get()->toArray();
        }

        return ArrayHelper::replace(self::openAiGeneratorsQuery()->get()->toArray(), $values);
    }

    public static function openAiGeneratorsValues(): array
    {
        return collect(
            OpenAIGenerator::select(['slug', 'title', 'filters', 'active'])
                ->where('active', true)
                ->where('slug', 'not like', 'ai_%')
                ->whereNotNull('filters')
                ->where('filters', '!=', '')
                ->get()
                ->groupBy('filters'))->toArray();

    }

    public function openAiItemsRules(string $prefix, array|string $rules = []): array
    {
        return collect(self::openAiGeneratorsQuery()->get()->groupBy('filters'))
            ->mapWithKeys(function ($group) use ($prefix, $rules) {
                return collect($group)->mapWithKeys(function ($item) use ($prefix, $rules) {
                    return [$prefix . $item->slug => $rules];
                })->toArray();
            })->toArray();

    }

    private static function getFreshData(): array
    {
        return [
            'active'                        => true,
            'name'                          => '',
            'price'                         => 0,
            'currency'                      => 'USD',
            'frequency'                     => FrequencyEnum::MONTHLY->value,
            'is_featured'                   => 0,
            'ai_name'                       => 'AI',
            'max_tokens'                    => null,
            'can_create_ai_images'          => true,
            'plan_type'                     => PlanType::ALL->value,
            'features'                      => [],
            'is_team_plan'                  => false,
            'user_api'                      => false,
            'plan_allow_seat'               => 0,
            'trial_days'                    => 0,
            'open_ai_items'                 => self::parseAiGenerator(self::openAiGeneratorsValues()),
            'description'                   => '',
            'plan_ai_tools'                 => self::parsePlanAiTools(MenuService::planAiToolsMenu()),
            'plan_features'                 => self::parsePlanFeatures(MenuService::planFeatureMenu()),
            'default_ai_model'              => EntityEnum::GPT_4_O->slug(),
            'ai_models'                     => EngineEnum::getNestedPlanLimits(),
            'hidden_url'                    => null,
            'hidden'                        => false,
            'reset_credits_on_renewal'      => false,
            'max_subscribe'                 => 0,
            'last_date'                     => null,
        ];
    }

    private static function parseAiGenerator(array $openAiData): array
    {
        $flattened = [];
        foreach ($openAiData as $items) {
            foreach ($items as $item) {
                $flattened[$item['slug']] = true;
            }
        }

        return $flattened;
    }

    private static function parsePlanAiTools(array $tools): array
    {
        return array_fill_keys(array_column($tools, 'key'), true);
    }

    private static function parsePlanFeatures(array $features): array
    {
        return array_fill_keys(array_column($features, 'key'), true);
    }

    public static function createFreshPlan(array $attributes = []): static
    {
        return (new self)->fill(array_merge(
            self::getFreshData(),
            [
                'type' => TypeEnum::SUBSCRIPTION->value,
            ],
            $attributes
        ));
    }

    public static function createFreshTokenPackPlan(array $attributes = []): static
    {
        return (new self)->fill(array_merge(
            self::getFreshData(),
            [
                'type' => TypeEnum::TOKEN_PACK->value,
            ],
            $attributes
        ));
    }
}
