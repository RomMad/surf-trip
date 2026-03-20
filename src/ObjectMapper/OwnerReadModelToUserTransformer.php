<?php

declare(strict_types=1);

namespace App\ObjectMapper;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\Model\TripWriteModel;
use App\ReadModel\Trip\TripOwnerReadModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<TripWriteModel, Trip>
 */
final readonly class OwnerReadModelToUserTransformer implements TransformCallableInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @param TripOwnerReadModel[] $owners
     * @param TripWriteModel       $source
     * @param Trip                 $target
     */
    public function __invoke(mixed $owners, object $source, ?object $target): mixed
    {
        return new ArrayCollection(
            array_map(
                fn (TripOwnerReadModel $owner): ?User => $this->entityManager->getReference(User::class, $owner->id),
                $owners,
            )
        );
    }
}
