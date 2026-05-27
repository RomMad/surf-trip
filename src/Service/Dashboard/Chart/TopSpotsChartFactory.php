<?php

declare(strict_types=1);

namespace App\Service\Dashboard\Chart;

use App\Dto\Dashboard\SpotStatDto;
use App\Dto\Dashboard\UserDashboardStatsDto;
use Symfony\UX\Chartjs\Model\Chart;

final readonly class TopSpotsChartFactory
{
    private const int MAX_SPOT_NAME_LENGTH = 12;

    public function __construct(
        private BarChartFactory $barChartFactory,
    ) {}

    public function create(UserDashboardStatsDto $stats): Chart
    {
        $labels = array_map(
            $this->formatSpotLabel(...),
            $stats->topSpots,
        );

        $data = array_map(
            static fn (SpotStatDto $spot): int => $spot->sessionsCount,
            $stats->topSpots,
        );

        return $this->barChartFactory->create($labels, $data, true);
    }

    private function formatSpotLabel(SpotStatDto $spot): string
    {
        $spotName = $this->truncateSpotName($spot->spot);

        if (null === $spot->averageRating) {
            return $spotName;
        }

        return sprintf('%s (%.1f)', $spotName, round($spot->averageRating, 1));
    }

    private function truncateSpotName(string $spotName): string
    {
        if (strlen($spotName) <= self::MAX_SPOT_NAME_LENGTH) {
            return $spotName;
        }

        return sprintf('%s…', substr($spotName, 0, self::MAX_SPOT_NAME_LENGTH));
    }
}
