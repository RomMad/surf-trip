<?php

declare(strict_types=1);

namespace App\Form\Model\SurfSession;

use App\Form\Model\Shared\Period;

final class SurfSessionSearchInput
{
    public ?string $query = null;

    public function __construct(
        public Period $period = new Period()
    ) {}
}
