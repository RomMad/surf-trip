<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\ReadModel\Trip\TripOwnerReadModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->password = $newHashedPassword;

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findOneByEmail(Email $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return TripOwnerReadModel[]
     */
    public function findOwnerReadModelsByQuery(string $query, int $limit = 10): array
    {
        return $this->createOwnerReadModelQueryBuilder()
            ->andWhere('
                    ILIKE(CONCAT(u.firstName, \' \', u.lastName), :filter) = TRUE
                    OR ILIKE(CONCAT(u.lastName, \' \', u.firstName), :filter) = TRUE
                ')
            ->setParameter('filter', '%'.$query.'%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int[] $ids
     *
     * @return TripOwnerReadModel[]
     */
    public function findOwnerReadModelsByIds(array $ids): array
    {
        if ([] === $ids) {
            return [];
        }

        return $this->createOwnerReadModelQueryBuilder()
            ->andWhere('u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }

    private function createOwnerReadModelQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->select(
                sprintf(
                    'new %s(
                        u.id,
                        CONCAT(u.firstName, \' \', u.lastName)
                    )',
                    TripOwnerReadModel::class
                )
            )
        ;
    }
}
