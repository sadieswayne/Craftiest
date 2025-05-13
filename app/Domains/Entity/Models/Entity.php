<?php

declare(strict_types=1);

namespace App\Domains\Entity\Models;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Enums\AITokenType;
use App\Enums\StatusEnum;
use App\Models\Finance\AiChatModelPlan;
use App\Models\Token;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    protected $fillable = [
        'title',
        'engine',
        'key',
        'status',
        'is_selected',
        'selected_title',
    ];

    protected $casts = [
        'engine'         => EngineEnum::class,
        'key'            => EntityEnum::class,
        'status'         => StatusEnum::class,
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }

    public function wordToken(): ?Token
    {
        return $this->tokens->firstWhere('type', AITokenType::WORD);
    }

    public function imageToken(): ?Token
    {
        return $this->tokens->firstWhere('type', AITokenType::IMAGE);
    }

    public function aiFinance(): HasMany
    {
        return $this->hasMany(AiChatModelPlan::class);
    }

    public function scopeIsEnabled(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::ENABLED);
    }

    public function scopeByEngine(Builder $query, EngineEnum $engine): Builder
    {
        return $query->where('engine', $engine);
    }

    public static function planModels(): Collection|array
    {
        return once(static function () {
            $planId = getCurrentActiveSubscription()?->getAttribute('plan_id') ?? 0;

            $query = self::query();

            if ($planId == 0) {
                $query->where('is_selected', 1);
            } else {
                $query->whereHas('aiFinance', function ($query) use ($planId) {
                    $query->where('plan_id', $planId);
                });
            }

            $query->whereHas('tokens', function ($query) {
                $query->where('type', 'word');
            });

            return $query->get();
        });
    }
}
