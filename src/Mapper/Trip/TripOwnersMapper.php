<?php

declare(strict_types=1);

namespace App\Mapper\Trip;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\Model\TripWriteModel;
use App\ReadModel\Trip\TripOwnerReadModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class TripOwnersMapper
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function map(TripWriteModel $tripWriteModel, Trip $trip): void
    {
        $users = new ArrayCollection(
            array_map(
                fn (TripOwnerReadModel $owner): ?User => $this->entityManager->getReference(User::class, $owner->id),
                $tripWriteModel->owners,
            )
        );

        $trip->setOwners($users);
    }
}
