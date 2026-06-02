<?php

declare(strict_types=1);

namespace App\Cache\Trip;

use App\Entity\User;

final class TripCacheKeys
{
    private const string READ_MODEL_PATTERN = 'trip.read_model.%d';
    private const string LIST_PATTERN = 'trip.list.user_%s.%s';

    public static function readModel(int $tripId): string
    {
        return sprintf(self::READ_MODEL_PATTERN, $tripId);
    }

    public static function list(string $querySlug, ?User $user = null): string
    {
        return sprintf(self::LIST_PATTERN, $user?->id ?: 'anonymous', $querySlug);
    }
}
