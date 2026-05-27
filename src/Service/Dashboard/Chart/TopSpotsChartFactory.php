<?php

declare(strict_types=1);

namespace App\Service\Dashboard\Chart;

use App\Dto\Dashboard\SpotStatDto;
use App\Dto\Dashboard\UserDashboardStatsDto;
use App\Enum\User\Locale;
use Symfony\UX\Chartjs\Model\Chart;

final readonly class TopSpotsChartFactory
{
    private const int MAX_SPOT_NAME_LENGTH = 14;

    public function __construct(
        private BarChartFactory $barChartFactory,
    ) {}

    public function create(UserDashboardStatsDto $stats, Locale $locale): Chart
    {
        $formatter = new \NumberFormatter(
            $locale->value,
            \NumberFormatter::DECIMAL
        );

        $labels = array_map(
            static fn (SpotStatDto $spot): string => self::formatSpotLabel($spot, $formatter),
            $stats->topSpots,
        );

        $data = array_map(
            static fn (SpotStatDto $spot): int => $spot->sessionsCount,
            $stats->topSpots,
        );

        return $this->barChartFactory->create($labels, $data, true);
    }

    private static function formatSpotLabel(SpotStatDto $spot, \NumberFormatter $formatter): string
    {
        $spotName = self::truncateSpotName($spot->spot);

        if (null === $spot->averageRating) {
            return $spotName;
        }

        return sprintf('%s (%s)', $spotName, $formatter->format($spot->averageRating));
    }

    private static function truncateSpotName(string $spotName): string
    {
        if (strlen($spotName) <= self::MAX_SPOT_NAME_LENGTH) {
            return $spotName;
        }

        return sprintf('%s…', substr($spotName, 0, self::MAX_SPOT_NAME_LENGTH));
    }
}
