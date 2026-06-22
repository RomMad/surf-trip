<?php

declare(strict_types=1);

namespace App\ReadModel;

final readonly class LocationReadModel
{
    public function __construct(
        public string $label,
        public float $latitude,
        public float $longitude,
    ) {}
}
