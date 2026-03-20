<?php

declare(strict_types=1);

namespace App\Pagination;

use App\Form\Model\TripFilter;
use App\ReadModel\Trip\TripIndexReadModel;
use App\Repository\TripRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class TripPager
{
    public const string CACHE_TAG = 'trip.pagination';
    private const string CACHE_KEY_PATTERN = 'trip.pagination.%s';
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
    public function create(TripFilter $filter, Request $request, int $maxPerPage = 10): Pagerfanta
    {
        return $this->cache->get(
            $this->generateCacheKey($request),
            function (ItemInterface $item) use ($filter, $request, $maxPerPage): Pagerfanta {
                $item->tag(self::CACHE_TAG);
                $item->expiresAfter(new \DateInterval(self::CACHE_TTL));

                $queryBuilder = $this->tripRepository->createOrderedQueryBuilder($filter);
                $countQueryBuilder = $this->tripRepository->getCountQueryBuilder($filter);

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
        $this->cache->invalidateTags([self::CACHE_TAG]);
    }

    private function generateCacheKey(Request $request): string
    {
        $queryString = $request->getQueryString() ?? '';
        $querySlug = $this->slugger->slug($queryString, '_')->toString();

        return sprintf(self::CACHE_KEY_PATTERN, $querySlug);
    }
}
