<?php

declare(strict_types=1);

namespace App\Dto\Dashboard;

final readonly class UserDashboardStatsDto
{
    /**
     * @param list<MonthlySessionStatDto> $monthlySessions
     * @param list<SpotStatDto>           $topSpots
     * @param list<YearlyActivityStatDto> $yearlyActivity
     */
    public function __construct(
        public DashboardKpisDto $kpis,
        public array $monthlySessions,
        public array $topSpots,
        public array $yearlyActivity,
    ) {}
}
