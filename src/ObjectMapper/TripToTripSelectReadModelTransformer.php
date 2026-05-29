<?php

declare(strict_types=1);

namespace App\ObjectMapper;

use App\Entity\Trip;
use App\ReadModel\Trip\TripSelectReadModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<Trip, TripSelectReadModel>
 */
final readonly class TripToTripSelectReadModelTransformer implements TransformCallableInterface
{
    public function __invoke(mixed $trip, object $source, ?object $target): mixed
    {
        if (!$trip instanceof Trip) {
            return null;
        }

        return new TripSelectReadModel(
            id: $trip->id,
            title: $trip->title->value,
            location: $trip->location->value,
        );
    }
}
