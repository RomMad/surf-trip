<?php

declare(strict_types=1);

namespace App\Pagination;

use App\Entity\User;
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
    public function create(Request $request, User $user, int $maxPerPage = 10): Pagerfanta
    {
        $queryBuilder = $this->surfSessionRepository->createOrderedQueryBuilder($user);
        $countQueryBuilder = $this->surfSessionRepository->getCountQueryBuilder($user);

        return $this->pagerFactory->createWithCountQueryBuilder(
            $queryBuilder,
            $countQueryBuilder,
            $request,
            $maxPerPage,
        );
    }
}
