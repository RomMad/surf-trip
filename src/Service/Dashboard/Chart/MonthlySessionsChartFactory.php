<?php

declare(strict_types=1);

namespace App\Service\Dashboard\Chart;

use App\Dto\Dashboard\MonthlySessionStatDto;
use App\Dto\Dashboard\UserDashboardStatsDto;
use App\Enum\User\Locale;
use Symfony\UX\Chartjs\Model\Chart;

final readonly class MonthlySessionsChartFactory
{
    public function __construct(
        private BarChartFactory $barChartFactory,
    ) {}

    public function create(UserDashboardStatsDto $stats, Locale $locale): Chart
    {
        $labels = array_map(
            fn (MonthlySessionStatDto $monthlySession): string => $this->formatMonthLabel(
                $monthlySession->monthStart,
                $locale
            ),
            $stats->monthlySessions,
        );

        $data = array_map(
            static fn (MonthlySessionStatDto $monthlySession): int => $monthlySession->sessionsCount,
            $stats->monthlySessions,
        );

        return $this->barChartFactory->create($labels, $data);
    }

    private function formatMonthLabel(\DateTimeImmutable $monthStart, Locale $locale): string
    {
        $formatter = new \IntlDateFormatter(
            $locale->value,
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            null,
            null,
            'MMM',
        );

        $label = $formatter->format($monthStart);

        return false === $label ? $monthStart->format('M') : (string) $label;
    }
}
