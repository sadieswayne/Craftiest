<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\EntityStats;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use App\Services\Common\MenuService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->shouldMigrate()) {
            $this->migrateCredits();
            $this->migratePlans();
            $this->migrateSlugs();
            $this->dropRemainingCreditColumn();
        }
    }

    private function shouldMigrate(): bool
    {
        return User::query()->exists();
    }

    private function migrateCredits(): void
    {
        $defaultWordModel = $this->defaultWordModel();
        $defaultImageModels = $this->defaultImageModels();

        User::query()
            ->chunk(10, function ($users) use ($defaultWordModel, $defaultImageModels) {
                foreach ($users as $user) {
                    $remainingWords = (int) $user->remaining_words;
                    $remainingImages = (int) $user->remaining_images;

                    if ($remainingWords === -1) {
                        EntityStats::word()->forUser($user)->list()->each(function ($entity) {
                            $entity->setUnlimited();
                        });
                    } else {
                        $driver = Entity::driver($defaultWordModel)->forUser($user);
                        $driver->setCredit($remainingWords);
                    }

                    if ($remainingImages === -1) {
                        EntityStats::image()->forUser($user)->list()->each(function ($entity) {
                            $entity->setUnlimited();
                        });
                    } elseif ($defaultImageModels) {
                        $defaultImageModelCount = count($defaultImageModels);
                        foreach ($defaultImageModels as $model) {
                            $driver = Entity::driver($model)->forUser($user);
                            $driver->setCredit(ceil($remainingImages / $defaultImageModelCount));
                        }
                    }
                }
            });

        if ($defaultWordModel === EntityEnum::GPT_4_O) {
            Setting::query()
                ->updateOrCreate([
                    'openai_default_model' => EntityEnum::GPT_4_O,
                ]);
        }
    }

    private function migratePlans(): void
    {
        $plans = Plan::get();

        foreach ($plans as $plan) {
            $this->updatePlan($plan);
        }
    }

    private function updatePlan($plan): void
    {
        if ($plan) {
            $allFeatures = array_merge($plan->plan_features, $plan->open_ai_items, $plan->plan_ai_tools);
            $updatedData = [
                'open_ai_items' => $this->getFreshJsonAiItems($allFeatures),
                'plan_ai_tools' => $this->getFreshJsonAiTools($allFeatures),
                'plan_features' => $this->getFreshJsonFeatures($allFeatures),
                'plan_type'     => strtolower($plan->plan_type),
                'ai_models'     => $this->convertPlanCredits($plan),
            ];
            $plan->update($updatedData);
        }
    }

    private function getFreshJsonAiItems($allFeatures): ?array
    {
        $flattened = [];
        foreach (Plan::openAiGeneratorsValues() as $items) {
            foreach ($items as $item) {
                if (in_array($item['slug'], $allFeatures, true)) {
                    $flattened[$item['slug']] = true;
                } else {
                    $flattened[$item['slug']] = false;
                }
            }
        }

        return $flattened;
    }

    private function getFreshJsonAiTools($allFeatures): ?array
    {
        $features = array_column(MenuService::planAiToolsMenu(), 'key');

        return array_combine($features, array_map(static fn ($feature) => in_array($feature, $allFeatures, true), $features));
    }

    private function getFreshJsonFeatures(array $allFeatures): ?array
    {
        $features = array_column(MenuService::planFeatureMenu(), 'key');

        return array_combine($features, array_map(static fn ($feature) => in_array($feature, $allFeatures, true), $features));
    }

    private function convertPlanCredits($plan): ?array
    {
        $aiModels = EngineEnum::getNestedPlanLimits();
        $defaultWordModel = $this->defaultWordModel();
        $defaultImageModels = $this->defaultImageModels();

        if ($plan->total_words === -1) {
            $aiModels[$defaultWordModel->engine()->slug()][$defaultWordModel->slug()]['isUnlimited'] = true;
        } else {
            $aiModels[$defaultWordModel->engine()->slug()][$defaultWordModel->slug()]['credit'] = $plan->total_words;
        }

        $defaultImageModelCount = count($defaultImageModels);
        $imageCreditPerModel = $defaultImageModelCount > 0
            ? ceil($plan->total_images / $defaultImageModelCount)
            : 0;

        foreach ($defaultImageModels as $model) {
            if ($plan->total_images === -1) {
                $aiModels[$model->engine()->slug()][$model->slug()]['isUnlimited'] = true;
            } else {
                $aiModels[$model->engine()->slug()][$model->slug()]['credit'] = $imageCreditPerModel;
            }
        }

        try {
            return $aiModels;
        } catch (JsonException $e) {
            Log::error("Error encoding AI models for plan ID {$plan->id}: " . $e->getMessage());

            return null;
        }
    }

    private function migrateSlugs(): void
    {
        SettingTwo::where('dalle', 'dalle3')->update(['dalle' => 'dall-e-3']);
        SettingTwo::where('dalle', 'dalle2')->update(['dalle' => 'dall-e-2']);
    }

    /**
     * not used for now
     * Convert a JSON array string to a JSON object string with keys set to true.
     */
    private function convertArrayToJsonObject(?string $jsonArrayString): ?string
    {
        if (is_null($jsonArrayString)) {
            return null;
        }
        $array = json_decode($jsonArrayString, true, 512, JSON_THROW_ON_ERROR);

        if (is_array($array) && array_keys($array) !== range(0, count($array) - 1)) {
            return $jsonArrayString;
        }

        $assocArray = [];
        foreach ((array) $array as $item) {
            $assocArray[$item] = true;
        }

        return json_encode($assocArray, JSON_THROW_ON_ERROR);
    }

    public function down(): void
    {
        if ($this->shouldMigrate()) {
            Schema::table('users', static function (Blueprint $table) {
                $table->integer('remaining_images')->nullable();
                $table->integer('remaining_words')->nullable();
            });
        }
    }

    public function dropRemainingCreditColumn(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remaining_words')) {
                $table->dropColumn(['remaining_words', 'remaining_images']);
            }
        });
    }

    public function defaultImageModels(): array
    {
        $settingsTwo = SettingTwo::query()->first();

        $models = [];

        $dalleModel = match ($settingsTwo?->dalle) {
            'dalle3' => EntityEnum::DALL_E_3->slug(),
            'dalle2' => EntityEnum::DALL_E_2->slug(),
            default  => $settingsTwo?->dalle ?? EntityEnum::DALL_E_2->slug(),
        };

        if (! setting('dalle_hidden') && $openAIModel = EntityEnum::fromSlug($dalleModel)) {
            $models['openai'] = $openAIModel;
        }

        $stableModel = $settingsTwo?->stablediffusion_default_model ?? $settingTwo?->stablediffusion_default_model ?? EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug();
        if (! setting('stable_hidden') && $stableModel = EntityEnum::fromSlug($stableModel)) {
            $models['stable'] = $stableModel;
        }

        $falModel = setting('fal_ai_default_model', 'flux-realism');
        if ($falModel = EntityEnum::fromSlug($falModel)) {
            $models['fal'] = $falModel;
        }

        return $models;
    }

    /** @noinspection PhpDuplicateMatchArmBodyInspection */
    public function defaultWordModel(): EntityEnum
    {
        $defaultEngine = setting('default_ai_engine');

        return match ($defaultEngine) {
            'openai'    => EntityEnum::GPT_4_O,
            'anthropic' => EntityEnum::fromSlug(setting('anthropic_default_model')),
            'gemini'    => EntityEnum::fromSlug(setting('gemini_default_model')),
            default     => EntityEnum::GPT_4_O,
        };
    }
};
