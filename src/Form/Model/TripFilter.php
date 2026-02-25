<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Enum\Trip\RequiredLevel;

final class TripFilter
{
    public ?string $search = null;

    public ?string $location = null;

    /** @var list<RequiredLevel> */
    public array $requiredLevels = [];
}
