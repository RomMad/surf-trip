<?php

declare(strict_types=1);

namespace App\Doctrine\Contract;

use App\Entity\User;

interface CreatedByInterface
{
    public function setCreatedBy(User $user): static;
}
