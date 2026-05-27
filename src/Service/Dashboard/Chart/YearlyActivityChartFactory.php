<?php

declare(strict_types=1);

namespace App\Service\Dashboard\Chart;

use App\Dto\Dashboard\UserDashboardStatsDto;
use App\Dto\Dashboard\YearlyActivityStatDto;
use App\Enum\User\Locale;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Model\Chart;

final readonly class YearlyActivityChartFactory
{
    private const string CHART1_COLOR_TOKEN = '--chart-1';
    private const string CHART2_COLOR_TOKEN = '--chart-2';
    private const int BAR_BORDER_RADIUS = 12;
    private const int BAR_THICKNESS = 16;
    private const int BAR_MAX_THICKNESS = 20;

    public function __construct(
        private BarChartFactory $barChartFactory,
        private TranslatorInterface $translator,
    ) {}

    public function create(UserDashboardStatsDto $stats, Locale $locale): Chart
    {
        $labels = array_map(
            static fn (YearlyActivityStatDto $year): string => $year->yearStart->format('Y'),
            $stats->yearlyActivity,
        );

        $sessionsData = array_map(
            static fn (YearlyActivityStatDto $year): int => $year->sessionsCount,
            $stats->yearlyActivity,
        );

        $tripsData = array_map(
            static fn (YearlyActivityStatDto $year): int => $year->tripsCount,
            $stats->yearlyActivity,
        );

        return $this->barChartFactory->create(
            labels: $labels,
            data: $sessionsData,
            datasets: [
                [
                    'label' => $this->translator->trans('surf_sessions.label', locale: $locale->value),
                    'data' => $sessionsData,
                    'backgroundColor' => self::CHART1_COLOR_TOKEN,
                    'borderColor' => self::CHART1_COLOR_TOKEN,
                    'borderRadius' => self::BAR_BORDER_RADIUS,
                    'barThickness' => self::BAR_THICKNESS,
                    'maxBarThickness' => self::BAR_MAX_THICKNESS,
                ],
                [
                    'label' => $this->translator->trans('trips.label', locale: $locale->value),
                    'data' => $tripsData,
                    'backgroundColor' => self::CHART2_COLOR_TOKEN,
                    'borderColor' => self::CHART2_COLOR_TOKEN,
                    'borderRadius' => self::BAR_BORDER_RADIUS,
                    'barThickness' => self::BAR_THICKNESS,
                    'maxBarThickness' => self::BAR_MAX_THICKNESS,
                ],
            ],
            optionsOverride: [
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                    ],
                ],
            ],
        );
    }
}
