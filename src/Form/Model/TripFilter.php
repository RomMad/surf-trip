<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Enum\User\SurfLevel;

final class TripFilter
{
    public ?string $search = null;

    public ?string $location = null;

    /** @var list<SurfLevel> */
    public array $requiredLevels = [];
}
