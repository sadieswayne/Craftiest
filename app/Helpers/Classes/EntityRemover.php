<?php

namespace App\Helpers\Classes;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Models\Entity;
use App\Models\Plan;
use App\Models\User;
use JsonException;
use RuntimeException;

class EntityRemover
{
    /**
     * @throws JsonException
     */
    public static function removeEntity(string $entitySlug): void
    {
        // Check if the entity exists in EntityEnum before deletion
        if (EntityEnum::tryFrom(str_replace('__', '.', $entitySlug))) {
            throw new RuntimeException('Entity exists in EntityEnum. Please remove it from there first.');
        }

        self::removeFromDatabase($entitySlug);
    }

    /**
     * @throws JsonException
     */
    private static function removeFromDatabase(string $entitySlug): void
    {
        self::removeFromEntityTable($entitySlug);
        self::removeFromTable(User::all(), 'entity_credits', $entitySlug);
        self::removeFromTable(Plan::all(), 'ai_models', $entitySlug);
        self::removeFromSettings($entitySlug);
    }

    private static function removeFromEntityTable(string $entitySlug): void
    {
        Entity::where('key', $entitySlug)->delete();
    }

    /**
     * @throws JsonException
     */
    private static function removeFromTable(iterable $records, string $attribute, string $entitySlug): void
    {
        $savedKey = null;

        foreach ($records as $record) {
            $data = $record->getAttributes()[$attribute] ?? null;

            $data = json_decode($data, true);
            if (! is_array($data)) {
                continue;
            }

            if ($savedKey === null) {
                foreach ($data as $key => $value) {
                    if (isset($value[$entitySlug])) {
                        $savedKey = $key;

                        break;
                    }
                }
                if ($savedKey !== null && isset($data[$savedKey][$entitySlug])) {
                    unset($data[$savedKey][$entitySlug]);
                    $record->forceFill([$attribute => $data])->save();
                }
            } elseif (isset($data[$savedKey][$entitySlug])) {
                unset($data[$savedKey][$entitySlug]);
                $record->forceFill([$attribute => $data])->save();
            }
        }
    }

    private static function removeFromSettings(string $entitySlug): void
    {
        $freeCreditsUponRegistration = setting('freeCreditsUponRegistration');
        $savedKey = null;

        if (! is_array($freeCreditsUponRegistration)) {
            return;
        }

        foreach ($freeCreditsUponRegistration as $key => $value) {
            if (isset($value[$entitySlug])) {
                $savedKey = $key;

                break;
            }
        }

        if ($savedKey !== null) {
            unset($freeCreditsUponRegistration[$savedKey][$entitySlug]);
            setting(['freeCreditsUponRegistration' => $freeCreditsUponRegistration])->save();
        }
    }
}
