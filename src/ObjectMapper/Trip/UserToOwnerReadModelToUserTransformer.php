<?php

declare(strict_types=1);

namespace App\ObjectMapper\Trip;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\Model\Trip\TripWriteModel;
use App\ReadModel\Trip\TripOwnerReadModel;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<Trip, TripWriteModel>
 */
final readonly class UserToOwnerReadModelToUserTransformer implements TransformCallableInterface
{
    /**
     * @param Trip           $source
     * @param TripWriteModel $target
     *
     * @return TripOwnerReadModel[]
     */
    public function __invoke(mixed $users, object $source, ?object $target): mixed
    {
        if (!$users instanceof Collection) {
            return [];
        }

        return $users->map(
            fn (User $user): TripOwnerReadModel => new TripOwnerReadModel(
                id: $user->id,
                fullName: $user->getFullName(),
            )
        )->toArray();
    }
}
