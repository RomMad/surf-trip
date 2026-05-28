<?php

declare(strict_types=1);

namespace App\Dto\Dashboard;

final readonly class YearlyActivityStatDto
{
    public function __construct(
        public \DateTimeImmutable $yearStart,
        public int $sessionsCount,
        public int $tripsCount,
    ) {}
}
