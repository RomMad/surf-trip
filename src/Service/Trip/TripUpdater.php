<?php

declare(strict_types=1);

namespace App\Service\Trip;

use App\Cache\Trip\TripCacheInvalidator;
use App\Entity\Trip;
use App\Exception\TripNotFoundHttpException;
use App\Form\Model\Trip\TripWriteModel;
use App\Repository\TripRepository;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class TripUpdater
{
    public function __construct(
        private TripRepository $tripRepository,
        private ObjectMapperInterface $objectMapper,
        private TripCacheInvalidator $tripCacheInvalidator,
    ) {}

    public function updateFromWriteModel(int $tripId, TripWriteModel $tripWriteModel): Trip
    {
        $trip = $this->tripRepository->findOneByIdWithOwners($tripId);

        if (null === $trip) {
            throw new TripNotFoundHttpException($tripId);
        }

        $this->objectMapper->map($tripWriteModel, $trip);

        $this->tripRepository->save($trip, true);

        $this->tripCacheInvalidator->invalidate($trip);

        return $trip;
    }
}
