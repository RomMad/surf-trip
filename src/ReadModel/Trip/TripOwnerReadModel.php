<?php

declare(strict_types=1);

namespace App\ReadModel\Trip;

final readonly class TripOwnerReadModel
{
    public function __construct(
        public int $id,
        public string $fullName,
    ) {}
}
