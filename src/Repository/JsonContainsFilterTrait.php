<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

trait JsonContainsFilterTrait
{
    /** @param list<mixed> $values */
    protected function addJsonArrayContains(QueryBuilder $queryBuilder, string $field, array $values): void
    {
        $orX = $queryBuilder->expr()->orX();
        $parameterPrefix = str_replace('.', '_', $field);

        foreach ($values as $index => $value) {
            $parameterName = sprintf('%s_%d', $parameterPrefix, $index);
            $orX->add(sprintf('CONTAINS(%s, :%s) = TRUE', $field, $parameterName));

            $queryBuilder->setParameter(
                $parameterName,
                json_encode($value)
            );
        }

        $queryBuilder->andWhere($orX);
    }
}
