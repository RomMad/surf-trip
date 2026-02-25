<?php

declare(strict_types=1);

namespace App\Pagination;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\CallbackAdapter;
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

        return $this->createPager($adapter, $request, $maxPerPage, $pageParameter);
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

        $adapter = new CallbackAdapter(
            static fn (): int => (int) $countQueryBuilder->getQuery()->getSingleScalarResult(),
            static function (int $offset, int $length) use ($resultQueryBuilder): iterable {
                $queryBuilder = clone $resultQueryBuilder;

                return $queryBuilder
                    ->setFirstResult($offset)
                    ->setMaxResults($length)
                    ->getQuery()
                    ->getResult()
                ;
            },
        );

        return $this->createPager($adapter, $request, $maxPerPage, $pageParameter);
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

    /**
     * @template T
     *
     * @param AdapterInterface<T> $adapter
     *
     * @return Pagerfanta<T>
     */
    private function createPager(AdapterInterface $adapter, Request $request, int $maxPerPage, string $pageParameter): Pagerfanta
    {
        return new Pagerfanta($adapter)
            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($request->query->getInt($pageParameter, 1))
            ->setNormalizeOutOfRangePages(true)
        ;
    }
}
