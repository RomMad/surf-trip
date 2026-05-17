<?php

declare(strict_types=1);

namespace App\Pagination;

use App\ReadModel\SurfSession\SurfSessionIndexReadModel;
use App\Repository\SurfSessionRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

final readonly class SurfSessionPager
{
    public function __construct(
        private SurfSessionRepository $surfSessionRepository,
        private PagerFactory $pagerFactory,
    ) {}

    /**
     * @return Pagerfanta<SurfSessionIndexReadModel>
     */
    public function create(Request $request, int $maxPerPage = 10): Pagerfanta
    {
        $queryBuilder = $this->surfSessionRepository->createOrderedQueryBuilder();
        $countQueryBuilder = $this->surfSessionRepository->getCountQueryBuilder();

        return $this->pagerFactory->createWithCountQueryBuilder(
            $queryBuilder,
            $countQueryBuilder,
            $request,
            $maxPerPage,
        );
    }
}
