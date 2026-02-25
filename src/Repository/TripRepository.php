<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Trip;
use App\Form\Model\TripFilter;
use App\ReadModel\Trip\TripIndexReadModel;
use App\ReadModel\Trip\TripShowReadModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    use JsonContainsFilterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function save(Trip $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trip $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createOrderedQueryBuilder(TripFilter $filter): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select(sprintf(
                'NEW %s(t.id, t.title, t.location, t.startAt, t.endAt, t.requiredLevels, t.description, t.createdAt)',
                TripIndexReadModel::class,
            ))
            ->orderBy('t.createdAt', 'DESC')
        ;

        $this->applyFilters($queryBuilder, $filter);

        return $queryBuilder;
    }

    public function getCountQueryBuilder(TripFilter $filter): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
        ;

        $this->applyFilters($queryBuilder, $filter);

        return $queryBuilder;
    }

    public function findShowReadModelById(int $id): ?TripShowReadModel
    {
        return $this->createQueryBuilder('t')
            ->select(sprintf(
                'NEW %s(
                    t.id,
                    t.title,
                    t.location,
                    t.startAt,
                    t.endAt,
                    t.requiredLevels,
                    t.description,
                    t.createdAt,
                    COALESCE(STRING_AGG(o.email, \', \' ORDER BY o.email), \'\')
                )',
                TripShowReadModel::class,
            ))
            ->groupBy('t.id')
            ->leftJoin('t.owner', 'o')

            ->andWhere('t.id = :id')
            ->setParameter('id', $id)

            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function applyFilters(QueryBuilder $queryBuilder, TripFilter $filter): void
    {
        if (null !== $filter->search) {
            $queryBuilder
                ->andWhere('ILIKE(t.title, :search) = TRUE OR ILIKE(t.description, :search) = TRUE')
                ->setParameter('search', '%'.$filter->search.'%')
            ;
        }

        if (null !== $filter->location) {
            $queryBuilder
                ->andWhere('ILIKE(t.location, :location) = TRUE')
                ->setParameter('location', '%'.$filter->location.'%')
            ;
        }

        if ([] !== $filter->requiredLevels) {
            $this->addJsonArrayContains($queryBuilder, 't.requiredLevels', $filter->requiredLevels);
        }
    }
}
