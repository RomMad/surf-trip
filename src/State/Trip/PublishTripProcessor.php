<?php

declare(strict_types=1);

namespace App\State\Trip;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Cache\Trip\TripCacheInvalidator;
use App\Entity\Trip;
use App\Repository\TripRepository;

/**
 * @implements ProcessorInterface<Trip, Trip>
 */
final readonly class PublishTripProcessor implements ProcessorInterface
{
    public function __construct(
        private TripRepository $tripRepository,
        private TripCacheInvalidator $tripCacheInvalidator,
    ) {}

    /**
     * @param Trip $trip
     */
    public function process(mixed $trip, Operation $operation, array $uriVariables = [], array $context = []): Trip
    {
        $trip->publish();

        $this->tripRepository->save($trip, true);
        $this->tripCacheInvalidator->invalidate($trip);

        return $trip;
    }
}
