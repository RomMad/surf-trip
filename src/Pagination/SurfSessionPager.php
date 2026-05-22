<?php

declare(strict_types=1);

namespace App\Pagination;

use App\Cache\SurfSession\SurfSessionCacheTags;
use App\Entity\User;
use App\Form\Model\SurfSession\SurfSessionSearchInput;
use App\ReadModel\SurfSession\SurfSessionIndexReadModel;
use App\Repository\SurfSessionRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class SurfSessionPager
{
    private const string CACHE_KEY_PATTERN = 'surf_session.list.%d.%s';
    private const string CACHE_TTL = 'PT5M';

    public function __construct(
        private SurfSessionRepository $surfSessionRepository,
        private PagerFactory $pagerFactory,
        private SluggerInterface $slugger,
        private TagAwareCacheInterface $cache,
    ) {}

    /**
     * @return Pagerfanta<SurfSessionIndexReadModel>
     */
    public function create(Request $request, User $user, SurfSessionSearchInput $searchInput, int $maxPerPage = 10): Pagerfanta
    {
        return $this->cache->get(
            $this->generateCacheKey($request, $user),
            function (ItemInterface $item) use ($request, $user, $searchInput, $maxPerPage): Pagerfanta {
                $item->tag(SurfSessionCacheTags::listForUser($user));
                $item->expiresAfter(new \DateInterval(self::CACHE_TTL));

                $queryBuilder = $this->surfSessionRepository->createOrderedQueryBuilder($user, $searchInput);
                $countQueryBuilder = $this->surfSessionRepository->getCountQueryBuilder($user, $searchInput);

                return $this->pagerFactory->createWithCountQueryBuilder(
                    $queryBuilder,
                    $countQueryBuilder,
                    $request,
                    $maxPerPage,
                );
            },
        );
    }

    private function generateCacheKey(Request $request, User $user): string
    {
        $queryString = $request->getQueryString() ?? '';
        $querySlug = $this->slugger->slug($queryString, '_')->toString();

        return sprintf(self::CACHE_KEY_PATTERN, $user->id, $querySlug);
    }
}
