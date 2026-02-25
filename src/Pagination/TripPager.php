<?php

declare(strict_types=1);

namespace App\Pagination;

use App\Form\Model\TripFilter;
use App\ReadModel\Trip\TripIndexReadModel;
use App\Repository\TripRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

final readonly class TripPager
{
    public function __construct(
        private TripRepository $tripRepository,
        private PagerFactory $pagerFactory,
    ) {}

    /**
     * @return Pagerfanta<TripIndexReadModel>
     */
    public function create(TripFilter $filter, Request $request, int $maxPerPage = 10): Pagerfanta
    {
        $queryBuilder = $this->tripRepository->createOrderedQueryBuilder($filter);
        $countQueryBuilder = $this->tripRepository->getCountQueryBuilder($filter);

        return $this->pagerFactory->createWithCountQueryBuilder(
            $queryBuilder,
            $countQueryBuilder,
            $request,
            $maxPerPage,
        );
    }
}
