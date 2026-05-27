<?php

declare(strict_types=1);

namespace App\Service;

use App\Cache\Dashboard\DashboardCacheTags;
use App\Dto\Dashboard\MonthlySessionStatDto;
use App\Dto\Dashboard\UserDashboardStatsDto;
use App\Dto\Dashboard\YearlyActivityStatDto;
use App\Entity\User;
use App\Repository\DashboardStatisticsRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class UserDashboardStatsProvider
{
    private const string CACHE_KEY_PATTERN = 'dashboard.stats.user.%d';
    private const string CACHE_TTL = 'PT15M';
    private const int YEARLY_ACTIVITY_YEARS = 10;

    public function __construct(
        private DashboardStatisticsRepository $dashboardStatisticsRepository,
        private TagAwareCacheInterface $cache,
    ) {}

    public function getForUser(User $user): UserDashboardStatsDto
    {
        return $this->cache->get(
            sprintf(self::CACHE_KEY_PATTERN, $user->id),
            function (ItemInterface $item) use ($user): UserDashboardStatsDto {
                $item->expiresAfter(new \DateInterval(self::CACHE_TTL));
                $item->tag(DashboardCacheTags::statsForUser($user));

                return $this->buildStatsForUser($user);
            },
        );
    }

    private function buildStatsForUser(User $user): UserDashboardStatsDto
    {
        $now = new \DateTimeImmutable();
        $yearStart = $now->setDate((int) $now->format('Y'), 1, 1)->setTime(0, 0);
        $nextYearStart = $yearStart->modify('+1 year');
        $yearlyPeriodStart = $yearStart->modify(sprintf('-%d years', self::YEARLY_ACTIVITY_YEARS - 1));
        $monthlyPeriodEnd = $now->modify('first day of next month')->setTime(0, 0);
        $monthlyPeriodStart = $monthlyPeriodEnd->modify('-12 months');

        $kpis = $this->dashboardStatisticsRepository->fetchKpis($user, $yearStart, $nextYearStart);

        $monthlySessions = $this->normalizeMonthlySessions(
            $this->dashboardStatisticsRepository->fetchMonthlySessionStats($user, $monthlyPeriodStart, $monthlyPeriodEnd),
            $monthlyPeriodStart,
        );

        $topSpots = $this->dashboardStatisticsRepository->fetchTopSpots($user);

        $yearlyActivity = $this->normalizeYearlyActivity(
            $this->dashboardStatisticsRepository->fetchYearlyActivityStats($user, $yearlyPeriodStart, $nextYearStart),
            $yearlyPeriodStart,
        );

        return new UserDashboardStatsDto(
            kpis: $kpis,
            monthlySessions: $monthlySessions,
            topSpots: $topSpots,
            yearlyActivity: $yearlyActivity,
        );
    }

    /**
     * @param list<MonthlySessionStatDto> $rows
     *
     * @return list<MonthlySessionStatDto>
     */
    private function normalizeMonthlySessions(array $rows, \DateTimeImmutable $periodStart): array
    {
        $byMonth = [];

        foreach ($rows as $row) {
            $byMonth[$row->monthStart->format('Y-m')] = $row;
        }

        $monthlySessions = [];

        for ($monthOffset = 0; $monthOffset < 12; ++$monthOffset) {
            $monthStart = $periodStart->modify(sprintf('+%d months', $monthOffset));
            $monthlySessions[] = $byMonth[$monthStart->format('Y-m')] ?? new MonthlySessionStatDto($monthStart, 0);
        }

        return $monthlySessions;
    }

    /**
     * @param list<YearlyActivityStatDto> $rows
     *
     * @return list<YearlyActivityStatDto>
     */
    private function normalizeYearlyActivity(array $rows, \DateTimeImmutable $periodStart): array
    {
        $byYear = [];

        foreach ($rows as $row) {
            $byYear[$row->yearStart->format('Y')] = $row;
        }

        $yearlyActivity = [];

        for ($yearOffset = 0; $yearOffset < self::YEARLY_ACTIVITY_YEARS; ++$yearOffset) {
            $yearStart = $periodStart->modify(sprintf('+%d years', $yearOffset));
            $yearlyActivity[] = $byYear[$yearStart->format('Y')] ?? new YearlyActivityStatDto($yearStart, 0, 0);
        }

        return $yearlyActivity;
    }
}
