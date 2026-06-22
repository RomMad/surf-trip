<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\Model\Trip\TripSearchInput;
use App\ReadModel\LocationReadModel;
use App\ReadModel\Trip\TripSelectReadModel;
use App\ReadModel\Trip\TripShowReadModel;
use App\Repository\Traits\PeriodFilterTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    use JsonContainsFilterTrait;
    use PeriodFilterTrait;

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

    public function findOneByIdWithOwners(int $id): ?Trip
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.owners', 'o')
            ->addSelect('o')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findShowReadModelById(int $id): ?TripShowReadModel
    {
        return $this->createDtoBaseQueryBuilder()
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return array<int, array{value: int, text: string}>
     */
    public function findTripChoicesByQuery(string $query, \DateTimeImmutable $referenceAt, User $user, int $limit = 10): array
    {
        $results = $this->createSelectReadModelBaseQueryBuilder($user)
            ->andWhere('t.startAt <= :referenceAt')
            ->andWhere('t.endAt >= :referenceAt')
            ->setParameter('referenceAt', $referenceAt)
            ->andWhere('ILIKE(t.title, :query) = TRUE OR ILIKE(t.location.label, :query) = TRUE')
            ->setParameter('query', '%'.$query.'%')

            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        return array_map(
            static fn (TripSelectReadModel $trip): array => [
                'value' => $trip->id,
                'text' => $trip->getLabel(),
            ],
            $results
        );
    }

    /**
     * @return TripSelectReadModel[]
     */
    public function findSelectReadModelsByUserAndTripId(User $user, int $tripId): array
    {
        return $this->createSelectReadModelBaseQueryBuilder($user)
            ->andWhere('t.id = :tripId')
            ->setParameter('tripId', $tripId)

            ->getQuery()
            ->getResult()
        ;
    }

    public function findSuggestedTripByDate(User $user, \DateTimeImmutable $referenceAt): ?TripSelectReadModel
    {
        return $this->createSelectReadModelBaseQueryBuilder($user)
            ->addSelect(
                'CASE
                    WHEN t.startAt <= :referenceAt AND t.endAt >= :referenceAt THEN 0
                    WHEN t.startAt > :referenceAt THEN 1
                    ELSE 2
                END AS HIDDEN relevance'
            )
            ->setParameter('referenceAt', $referenceAt)
            ->orderBy('relevance', 'ASC')
            ->addOrderBy('t.startAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function createOrderedQueryBuilder(TripSearchInput $searchInput, ?User $user = null): QueryBuilder
    {
        $queryBuilder = $this->createDtoBaseQueryBuilder()
            ->orderBy('t.id', 'DESC')
        ;

        $this->applyFilters($queryBuilder, $searchInput, $user);

        return $queryBuilder;
    }

    public function getCountQueryBuilder(TripSearchInput $searchInput, ?User $user = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
        ;

        $this->applyFilters($queryBuilder, $searchInput, $user);

        return $queryBuilder;
    }

    private function createDtoBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->select(sprintf(
                'NEW %s(
                    t.id,
                    t.slug,
                    t.title,
                    NEW %s(
                        t.location.label,
                        t.location.latitude,
                        t.location.longitude
                    ),
                    t.startAt,
                    t.endAt,
                    t.requiredLevels,
                    t.description,
                    t.createdAt,
                    JSON_AGG(
                        JSON_BUILD_ARRAY(
                            o.id,
                            CONCAT(o.firstName, \' \', o.lastName)
                        )
                        ORDER BY o.firstName, o.lastName
                    )
                )',
                TripShowReadModel::class,
                LocationReadModel::class,
            ))
            ->leftJoin('t.owners', 'o')
            ->groupBy('t.id')
        ;
    }

    private function createSelectReadModelBaseQueryBuilder(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->select(sprintf(
                'NEW %s(
                    t.id,
                    t.title,
                    t.location.label
                )',
                TripSelectReadModel::class,
            ))
            ->leftJoin('t.owners', 'o')

            ->where('o.id = :userId')
            ->setParameter('userId', $user->id)

            ->addOrderBy('t.startAt', 'ASC')
            ->addOrderBy('t.title', 'ASC')
        ;
    }

    private function applyFilters(QueryBuilder $queryBuilder, TripSearchInput $searchInput, ?User $user): void
    {
        if ($searchInput->myTripsOnly && null !== $user) {
            $membershipsQueryBuilder = $this->createQueryBuilder('tm')
                ->select('1')
                ->innerJoin('tm.owners', 'tmu')
                ->where('tm = t')
                ->andWhere('tmu = :ownerUser')
            ;

            $queryBuilder
                ->andWhere($queryBuilder->expr()->exists($membershipsQueryBuilder->getDQL()))
                ->setParameter('ownerUser', $user)
            ;
        }

        if (null !== $searchInput->query) {
            $queryBuilder
                ->andWhere('ILIKE(t.title, :search) = TRUE OR ILIKE(t.description, :search) = TRUE')
                ->setParameter('search', '%'.$searchInput->query.'%')
            ;
        }

        $this->applyPeriodFilters($queryBuilder, $searchInput->period, 't.startAt', 't.endAt');

        if (null !== $searchInput->location) {
            $queryBuilder
                ->andWhere(
                    'ILIKE(
                    CONCAT(
                        t.location.label, \' \',
                        COALESCE(t.location.comment, \'\')
                    ),
                    :location) = TRUE'
                )
                ->setParameter('location', '%'.$searchInput->location.'%')
            ;
        }

        if ([] !== $searchInput->requiredLevels) {
            $this->addJsonArrayContains($queryBuilder, 't.requiredLevels', $searchInput->requiredLevels);
        }
    }
}
