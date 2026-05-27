<?php

declare(strict_types=1);

namespace App\Cache\Dashboard;

use App\Entity\Trip;
use App\Entity\User;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class DashboardCacheInvalidator
{
    public function __construct(
        private TagAwareCacheInterface $cache,
    ) {}

    public function invalidateForUser(User $user): void
    {
        $this->cache->invalidateTags([DashboardCacheTags::statsForUser($user)]);
    }

    public function invalidateForTrip(Trip $trip): void
    {
        foreach ($trip->owners as $owner) {
            $this->invalidateForUser($owner);
        }
    }
}
