<?php

declare(strict_types=1);

namespace App\ObjectMapper;

use App\Entity\Trip;
use App\ReadModel\Trip\TripSelectReadModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<TripSelectReadModel, Trip>
 */
final readonly class TripSelectReadModelToTripTransformer implements TransformCallableInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(mixed $tripSelect, object $source, ?object $target): mixed
    {
        if (!$tripSelect instanceof TripSelectReadModel) {
            return null;
        }

        return $this->entityManager->getReference(Trip::class, $tripSelect->id);
    }
}
