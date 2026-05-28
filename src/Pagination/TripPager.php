<?php

declare(strict_types=1);

namespace App\Pagination;

use App\Cache\Trip\TripCacheKeys;
use App\Cache\Trip\TripCacheTags;
use App\Form\Model\Trip\TripSearchInput;
use App\ReadModel\Trip\TripIndexReadModel;
use App\Repository\TripRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class TripPager
{
    private const string CACHE_TTL = 'PT5M';

    public function __construct(
        private TripRepository $tripRepository,
        private PagerFactory $pagerFactory,
        private SluggerInterface $slugger,
        private TagAwareCacheInterface $cache,
    ) {}

    /**
     * @return Pagerfanta<TripIndexReadModel>
     */
    public function create(TripSearchInput $searchInput, Request $request, int $maxPerPage = 10): Pagerfanta
    {
        return $this->cache->get(
            $this->generateCacheKey($request),
            function (ItemInterface $item) use ($searchInput, $request, $maxPerPage): Pagerfanta {
                $item->tag(TripCacheTags::LIST);
                $item->expiresAfter(new \DateInterval(self::CACHE_TTL));

                $queryBuilder = $this->tripRepository->createOrderedQueryBuilder($searchInput);
                $countQueryBuilder = $this->tripRepository->getCountQueryBuilder($searchInput);

                return $this->pagerFactory->createWithCountQueryBuilder(
                    $queryBuilder,
                    $countQueryBuilder,
                    $request,
                    $maxPerPage,
                );
            },
        );
    }

    public function invalidate(): void
    {
        $this->cache->invalidateTags([TripCacheTags::LIST]);
    }

    private function generateCacheKey(Request $request): string
    {
        $queryString = $request->getQueryString() ?? '';
        $querySlug = $this->slugger->slug($queryString, '_')->toString();

        return TripCacheKeys::list($querySlug);
    }
}
