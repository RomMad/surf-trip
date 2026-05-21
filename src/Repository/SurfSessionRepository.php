<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SurfSession;
use App\Entity\User;
use App\Form\Model\SurfSession\SurfSessionSearchInput;
use App\ReadModel\SurfSession\SurfSessionIndexReadModel;
use App\Repository\Traits\PeriodFilterTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SurfSession>
 */
final class SurfSessionRepository extends ServiceEntityRepository
{
    use PeriodFilterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurfSession::class);
    }

    public function save(SurfSession $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SurfSession $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createOrderedQueryBuilder(User $user, SurfSessionSearchInput $searchInput): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select(sprintf(
                'NEW %s(
                    s.id,
                    s.spot,
                    s.board,
                    s.startAt,
                    s.endAt,
                    s.rating,
                    s.objective,
                    s.comment
                )',
                SurfSessionIndexReadModel::class,
            ))
            ->orderBy('s.startAt', 'DESC')
            ->where('s.user = :user')
            ->setParameter('user', $user)
        ;

        $this->applyFilters($queryBuilder, $searchInput);

        return $queryBuilder;
    }

    public function getCountQueryBuilder(User $user, SurfSessionSearchInput $searchInput): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.user = :user')
            ->setParameter('user', $user)
        ;

        $this->applyFilters($queryBuilder, $searchInput);

        return $queryBuilder;
    }

    private function applyFilters(QueryBuilder $queryBuilder, SurfSessionSearchInput $searchInput): QueryBuilder
    {
        if ($searchInput->query) {
            $queryBuilder
                ->andWhere('ILIKE(s.spot, :query) = TRUE OR ILIKE(s.board, :query) = TRUE')
                ->setParameter('query', '%'.$searchInput->query.'%')
            ;
        }

        $this->applyPeriodFilters($queryBuilder, $searchInput->period, 's.startAt', 's.endAt');

        return $queryBuilder;
    }
}
