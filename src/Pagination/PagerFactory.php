<?php

declare(strict_types=1);

namespace App\Pagination;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

final readonly class PagerFactory
{
    private const int DEFAULT_MAX_PER_PAGE = 10;
    private const string DEFAULT_PAGE_PARAMETER = 'page';

    /**
     * @return Pagerfanta<mixed>
     */
    public function create(
        QueryBuilder $queryBuilder,
        Request $request,
        int $maxPerPage = self::DEFAULT_MAX_PER_PAGE,
        string $pageParameter = self::DEFAULT_PAGE_PARAMETER,
    ): Pagerfanta {
        $this->sortQueryBuilder($queryBuilder, $request);

        $adapter = new QueryAdapter($queryBuilder);
        $currentPage = $this->getCurrentPage($request, $pageParameter);

        return $this->createPager($adapter, $currentPage, $maxPerPage);
    }

    /**
     * @return Pagerfanta<mixed>
     */
    public function createWithCountQueryBuilder(
        QueryBuilder $resultQueryBuilder,
        QueryBuilder $countQueryBuilder,
        Request $request,
        int $maxPerPage = self::DEFAULT_MAX_PER_PAGE,
        string $pageParameter = self::DEFAULT_PAGE_PARAMETER,
    ): Pagerfanta {
        $this->sortQueryBuilder($resultQueryBuilder, $request);

        $currentPage = $this->getCurrentPage($request, $pageParameter);
        $offset = ($currentPage - 1) * $maxPerPage;

        $nbResults = $this->getCount($countQueryBuilder);
        $results = $this->getSlice($resultQueryBuilder, $offset, $maxPerPage);

        $adapter = new FixedAdapter($nbResults, $results);

        return $this->createPager($adapter, $currentPage, $maxPerPage);
    }

    private function sortQueryBuilder(QueryBuilder $queryBuilder, Request $request): void
    {
        if (!$request->query->has('sort')) {
            return;
        }

        $sort = $request->query->get('sort');
        $direction = $request->query->get('direction', 'asc');

        if (in_array($direction, ['asc', 'desc'], true)) {
            $queryBuilder->orderBy($sort, $direction);
        }
    }

    private function getCurrentPage(Request $request, string $pageParameter): int
    {
        return max(1, $request->query->getInt($pageParameter, 1));
    }

    private function getCount(QueryBuilder $queryBuilder): int
    {
        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    private function getSlice(QueryBuilder $queryBuilder, int $offset, int $limit): array
    {
        return $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @template T
     *
     * @param AdapterInterface<T> $adapter
     *
     * @return Pagerfanta<T>
     */
    private function createPager(AdapterInterface $adapter, int $currentPage, int $maxPerPage): Pagerfanta
    {
        return new Pagerfanta($adapter)
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($maxPerPage)
            ->setNormalizeOutOfRangePages(true)
        ;
    }
}
