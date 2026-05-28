<?php

declare(strict_types=1);

namespace App\Dto\Dashboard;

final readonly class SpotStatDto
{
    public function __construct(
        public string $spot,
        public int $sessionsCount,
        public ?float $averageRating,
    ) {}
}
