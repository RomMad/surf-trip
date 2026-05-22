<?php

declare(strict_types=1);

namespace App\Cache\SurfSession;

use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class SurfSessionCacheInvalidator
{
    public function __construct(
        private TagAwareCacheInterface $cache,
    ) {}

    public function invalidateList(): void
    {
        $this->cache->invalidateTags([SurfSessionCacheTags::LIST]);
    }
}
