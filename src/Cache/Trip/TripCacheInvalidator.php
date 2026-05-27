<?php

declare(strict_types=1);

namespace App\Cache\Trip;

use App\Cache\Dashboard\DashboardCacheInvalidator;
use App\Entity\Trip;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class TripCacheInvalidator
{
    public function __construct(
        private TagAwareCacheInterface $cache,
        private DashboardCacheInvalidator $dashboardCacheInvalidator,
    ) {}

    public function invalidate(Trip $trip): void
    {
        $this->cache->delete(TripCacheKeys::readModel($trip->id));
        $this->cache->invalidateTags([TripCacheTags::LIST]);

        $this->dashboardCacheInvalidator->invalidateForTrip($trip);
    }
}
