<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SurfSession;
use App\Entity\User;
use App\ReadModel\SurfSession\SurfSessionIndexReadModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SurfSession>
 */
final class SurfSessionRepository extends ServiceEntityRepository
{
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

    public function createOrderedQueryBuilder(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('s')
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
    }

    public function getCountQueryBuilder(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.user = :user')
            ->setParameter('user', $user)
        ;
    }
}
