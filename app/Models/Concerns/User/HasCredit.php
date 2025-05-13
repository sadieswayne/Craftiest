<?php

namespace App\Models\Concerns\User;

use App\Domains\Entity\Facades\Entity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Yediyuz\Helpers\ArrayHelper;

trait HasCredit
{
    protected function entityCredits(): Attribute
    {
        $getCredits = static function (?string $value) {
            $freshCredits = self::getFreshCredits();

            if ($value !== null) {
                $credits = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                $credits = ArrayHelper::replace($freshCredits, Arr::wrap($credits));
            } else {
                $credits = $freshCredits;
            }

            return $credits;
        };

        $setCredits = static function (?array $creditsArray) {
            if (is_null($creditsArray)) {
                $creditsArray = self::getFreshCredits();
            }
            array_walk_recursive($creditsArray, static function (&$value, $key) {
                switch ($key) {
                    case 'credit':
                        $value = max((float) $value, 0.0);  // Ensure credit is non-negative

                        break;
                    case 'isUnlimited':
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

                        break;
                }
            });

            return json_encode($creditsArray, JSON_THROW_ON_ERROR);
        };

        return Attribute::make(
            get: $getCredits,
            set: $setCredits
        );
    }

    public function getCredit(string $engineKey, string $entityKey): array
    {
        return $this->entity_credits[$engineKey][$entityKey] ?? [
            'credit'      => 0,
            'isUnlimited' => false,
        ];
    }

    public static function getFreshCredits($credit = 0): array
    {
        return once(static function () use ($credit) {
            return Entity::all()
                ->includeUnlisted(false)
                ->groupBy(function ($entity) {
                    return $entity->engine();  // Group by engine
                })
                ->map(function ($groupedEntities) use ($credit) {
                    return $groupedEntities->mapWithKeys(function ($entity) use ($credit) {
                        return [
                            $entity->creditKey() => [
                                'credit'      => $credit,
                                'isUnlimited' => false,
                            ],
                        ];
                    });
                })->toArray();
        });
    }

    /**
     * Update the credits for the user with type casting.
     */
    public function updateCredits(array $newCredits): void
    {
        $this->entity_credits = $newCredits;
        $this->save();
    }
}
