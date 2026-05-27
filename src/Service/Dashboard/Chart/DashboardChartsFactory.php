<?php

declare(strict_types=1);

namespace App\Service\Dashboard\Chart;

use App\Dto\Dashboard\UserDashboardStatsDto;
use App\Enum\User\Locale;
use Symfony\UX\Chartjs\Model\Chart;

final readonly class DashboardChartsFactory
{
    public function __construct(
        private MonthlySessionsChartFactory $monthlySessionsChartFactory,
        private TopSpotsChartFactory $topSpotsChartFactory,
        private YearlyActivityChartFactory $yearlyActivityChartFactory,
    ) {}

    /**
     * @return array{monthly_sessions: Chart, top_spots: Chart, yearly_activity: Chart}
     */
    public function createAll(UserDashboardStatsDto $stats, Locale $locale): array
    {
        return [
            'monthly_sessions' => $this->monthlySessionsChartFactory->create($stats, $locale),
            'top_spots' => $this->topSpotsChartFactory->create($stats),
            'yearly_activity' => $this->yearlyActivityChartFactory->create($stats, $locale),
        ];
    }
}
