<?php

declare(strict_types=1);

namespace App\ObjectMapper\Location;

use App\Entity\Embeddable\Location;
use App\Entity\Trip;
use App\Form\Model\Shared\LocationInput;
use App\Form\Model\Trip\TripWriteModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<Trip, TripWriteModel>
 */
final readonly class LocationToLocationInputTransformer implements TransformCallableInterface
{
    /**
     * @param Location $location
     *
     * @return LocationInput
     */
    public function __invoke(mixed $location, object $source, ?object $target): mixed
    {
        $locationInput = new LocationInput();
        $locationInput->label = $location->label;
        $locationInput->latitude = $location->latitude;
        $locationInput->longitude = $location->longitude;
        $locationInput->placeId = $location->placeId;
        $locationInput->comment = $location->comment;

        return $locationInput;
    }
}
