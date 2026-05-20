<?php

declare(strict_types=1);

namespace App\Form\Model\Trip;

use App\Enum\User\SurfLevel;

final class TripSearchInput
{
    public ?string $query = null;

    public ?string $location = null;

    /** @var list<SurfLevel> */
    public array $requiredLevels = [];
}
