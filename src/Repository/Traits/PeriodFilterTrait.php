<?php

declare(strict_types=1);

namespace App\Repository\Traits;

use App\Form\Model\Shared\Period;
use Doctrine\ORM\QueryBuilder;

trait PeriodFilterTrait
{
    /**
     * Add filters to the query builder based on the provided period.
     */
    private function applyPeriodFilters(QueryBuilder $queryBuilder, Period $period, string $startField, string $endField): void
    {
        if (null !== $period->from) {
            $queryBuilder
                ->andWhere(sprintf('%s >= :periodFrom', $startField))
                ->setParameter('periodFrom', $period->from)
            ;
        }

        if (null !== $period->to) {
            $queryBuilder
                ->andWhere(sprintf('%s <= :periodTo', $endField))
                ->setParameter('periodTo', $period->to)
            ;
        }
    }
}
