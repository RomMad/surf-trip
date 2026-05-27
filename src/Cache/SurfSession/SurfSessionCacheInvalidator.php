<?php

declare(strict_types=1);

namespace App\Cache\SurfSession;

use App\Cache\Dashboard\DashboardCacheInvalidator;
use App\Entity\User;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class SurfSessionCacheInvalidator
{
    public function __construct(
        private TagAwareCacheInterface $cache,
        private DashboardCacheInvalidator $dashboardCacheInvalidator,
    ) {}

    public function invalidateList(User $user): void
    {
        $this->cache->invalidateTags([SurfSessionCacheTags::listForUser($user)]);
        $this->dashboardCacheInvalidator->invalidateForUser($user);
    }
}
