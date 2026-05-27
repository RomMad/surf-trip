<?php

declare(strict_types=1);

namespace App\Dto\Dashboard;

final readonly class DashboardKpisDto
{
    public function __construct(
        public int $totalTrips,
        public int $tripsThisYear,
        public int $totalSessions,
        public int $sessionsThisYear,
        public ?float $averageSessionRating,
    ) {}
}
