<?php

namespace App\Models\Finance;

use App\Domains\Entity\Models\Entity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AiChatModelPlan extends Pivot
{
    protected $table = 'ai_chat_model_plans';

    public $timestamps = false;

    protected $fillable = [
        'entity_id',
        'plan_id',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
