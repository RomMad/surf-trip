<?php

declare(strict_types=1);

namespace App\Cache\Trip;

final class TripCacheKeys
{
    private const string READ_MODEL_PATTERN = 'trip.read_model.%d';
    private const string LIST_PATTERN = 'trip.list.%s';

    public static function readModel(int $tripId): string
    {
        return sprintf(self::READ_MODEL_PATTERN, $tripId);
    }

    public static function list(string $querySlug): string
    {
        return sprintf(self::LIST_PATTERN, $querySlug);
    }
}
