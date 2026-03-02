<?php

declare(strict_types=1);

namespace App\ReadModel\Trip;

use App\Entity\User;

interface TripOwnershipAwareInterface
{
    public function isOwnedByUser(User $user): bool;
}
