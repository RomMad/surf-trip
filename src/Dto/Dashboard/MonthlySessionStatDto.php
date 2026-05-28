<?php

declare(strict_types=1);

namespace App\Dto\Dashboard;

final readonly class MonthlySessionStatDto
{
    public function __construct(
        public \DateTimeImmutable $monthStart,
        public int $sessionsCount,
    ) {}
}
