<?php

declare(strict_types=1);

namespace App\Cache\SurfSession;

use App\Entity\User;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class SurfSessionCacheInvalidator
{
    public function __construct(
        private TagAwareCacheInterface $cache,
    ) {}

    public function invalidateList(User $user): void
    {
        $this->cache->invalidateTags([SurfSessionCacheTags::listForUser($user)]);
    }
}
