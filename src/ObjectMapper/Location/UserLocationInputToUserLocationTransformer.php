<?php

declare(strict_types=1);

namespace App\ObjectMapper\Location;

use App\Entity\Embeddable\UserLocation;
use App\Entity\User;
use App\Form\Model\User\ProfileWriteModel;
use App\Form\Model\User\UserLocationInput;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<ProfileWriteModel, User>
 */
final readonly class UserLocationInputToUserLocationTransformer implements TransformCallableInterface
{
    /**
     * @param UserLocationInput|null $locationInput
     *
     * @return UserLocation|null
     */
    public function __invoke(mixed $locationInput, object $source, ?object $target): mixed
    {
        if (null === $locationInput || '' === $locationInput->label) {
            return null;
        }

        return new UserLocation(
            label: $locationInput->label,
            latitude: $locationInput->latitude,
            longitude: $locationInput->longitude,
            placeId: $locationInput->placeId,
        );
    }
}
