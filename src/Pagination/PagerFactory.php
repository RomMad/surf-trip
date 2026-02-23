<?php

namespace App\Pagination;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

final class PagerFactory
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

        return new Pagerfanta(
            new QueryAdapter($queryBuilder)
        )
            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($request->query->getInt($pageParameter, 1))
            ->setNormalizeOutOfRangePages(true)
        ;
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
}
