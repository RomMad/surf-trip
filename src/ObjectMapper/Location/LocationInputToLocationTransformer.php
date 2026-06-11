<?php

declare(strict_types=1);

namespace App\ObjectMapper\Location;

use App\Entity\Embeddable\Location;
use App\Entity\Trip;
use App\Form\Model\Trip\TripWriteModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<TripWriteModel, Trip>
 */
final readonly class LocationInputToLocationTransformer implements TransformCallableInterface
{
    /**
     * @return Location
     */
    public function __invoke(mixed $locationInput, object $source, ?object $target): mixed
    {
        return new Location(
            label: $locationInput->label,
            latitude: $locationInput->latitude,
            longitude: $locationInput->longitude,
            placeId: $locationInput->placeId,
        );
    }
}
