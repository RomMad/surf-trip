<?php

declare(strict_types=1);

namespace App\State\Trip;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Cache\Trip\TripCacheInvalidator;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<Trip, Trip>
 */
final readonly class PublishTripProcessor implements ProcessorInterface
{
    public function __construct(
        private TripRepository $tripRepository,
        private TripCacheInvalidator $tripCacheInvalidator,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Trip
    {
        $tripId = $uriVariables['id'] ?? null;

        if (!$tripId) {
            throw new \InvalidArgumentException('Trip ID is required');
        }

        $trip = $this->tripRepository->find($tripId);

        if (null === $trip) {
            throw new NotFoundHttpException('Trip not found');
        }

        $trip->publish();

        $this->tripRepository->save($trip, true);
        $this->tripCacheInvalidator->invalidate($trip);

        return $trip;
    }
}
