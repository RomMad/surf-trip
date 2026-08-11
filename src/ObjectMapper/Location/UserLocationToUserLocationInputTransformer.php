<?php

declare(strict_types=1);

namespace App\ObjectMapper\Location;

use App\Entity\Embeddable\UserLocation;
use App\Entity\User;
use App\Form\Model\User\ProfileWriteModel;
use App\Form\Model\User\UserLocationInput;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<User, ProfileWriteModel>
 */
final readonly class UserLocationToUserLocationInputTransformer implements TransformCallableInterface
{
    /**
     * @param UserLocation|null $location
     *
     * @return UserLocationInput|null
     */
    public function __invoke(mixed $location, object $source, ?object $target): mixed
    {
        if (null === $location) {
            return null;
        }

        $locationInput = new UserLocationInput();
        $locationInput->label = $location->label ?? '';
        $locationInput->latitude = $location->latitude;
        $locationInput->longitude = $location->longitude;
        $locationInput->placeId = $location->placeId;

        return $locationInput;
    }
}
