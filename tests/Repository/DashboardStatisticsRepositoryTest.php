<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Enum\SurfSession\SurfSessionRating;
use App\Repository\DashboardStatisticsRepository;
use App\Tests\CustomKernelTestCase;
use App\Tests\Fixtures\DashboardStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;

/**
 * @internal
 */
#[CoversClass(DashboardStatisticsRepository::class)]
#[Medium]
final class DashboardStatisticsRepositoryTest extends CustomKernelTestCase
{
    private ?DashboardStatisticsRepository $repository = null;

    protected function setUp(): void
    {
        parent::setUp();

        DashboardStory::load();

        $this->repository = $this->getContainer()->get(DashboardStatisticsRepository::class);
    }

    public function testFetchKpisWithExistingDataset(): void
    {
        $user = DashboardStory::getDashboardUser();
        $yearStart = new \DateTimeImmutable(sprintf('%d-01-01 00:00:00', (int) new \DateTimeImmutable()->format('Y')));
        $nextYearStart = $yearStart->modify('+1 year');

        $kpis = $this->repository->fetchKpis($user, $yearStart, $nextYearStart);

        $this->assertSame(2, $kpis->totalTrips);
        $this->assertSame(1, $kpis->tripsThisYear);
        $this->assertSame(4, $kpis->totalSessions);
        $this->assertSame(3, $kpis->sessionsThisYear);
        $this->assertNotNull($kpis->averageSessionRating);
        $this->assertSame(3.7, $kpis->averageSessionRating);
    }

    public function testFetchMonthlySessionStatsWithExistingDataset(): void
    {
        $user = DashboardStory::getDashboardUser();
        $currentYear = (int) new \DateTimeImmutable()->format('Y');
        $previousYear = $currentYear - 1;
        $periodStart = new \DateTimeImmutable(sprintf('%d-01-01 00:00:00', $previousYear));
        $periodEnd = new \DateTimeImmutable(sprintf('%d-12-31 23:59:59', $currentYear))->modify('+1 second');

        $results = $this->repository->fetchMonthlySessionStats($user, $periodStart, $periodEnd);

        $this->assertCount(4, $results);
        $this->assertSame(sprintf('%d-04-01', $previousYear), $results[0]->monthStart->format('Y-m-d'));
        $this->assertSame(1, $results[0]->sessionsCount);
        $this->assertSame(sprintf('%d-01-01', $currentYear), $results[1]->monthStart->format('Y-m-d'));
        $this->assertSame(1, $results[1]->sessionsCount);
        $this->assertSame(sprintf('%d-02-01', $currentYear), $results[2]->monthStart->format('Y-m-d'));
        $this->assertSame(1, $results[2]->sessionsCount);
        $this->assertSame(sprintf('%d-03-01', $currentYear), $results[3]->monthStart->format('Y-m-d'));
        $this->assertSame(1, $results[3]->sessionsCount);
    }

    public function testFetchTopSpotsWithExistingDataset(): void
    {
        $user = DashboardStory::getDashboardUser();
        $limit = 5;

        $results = $this->repository->fetchTopSpots($user, $limit);

        $this->assertCount(3, $results);
        $this->assertSame('Hossegor', $results[0]->spot);
        $this->assertSame(2, $results[0]->sessionsCount);
        $this->assertEqualsWithDelta((float) SurfSessionRating::Good->value, $results[0]->averageRating, 0.0001);

        $this->assertSame('Biarritz', $results[1]->spot);
        $this->assertSame(1, $results[1]->sessionsCount);
        $this->assertEqualsWithDelta((float) SurfSessionRating::Excellent->value, $results[1]->averageRating, 0.0001);

        $this->assertSame('La Torche', $results[2]->spot);
        $this->assertSame(1, $results[2]->sessionsCount);
        $this->assertEqualsWithDelta((float) SurfSessionRating::Bad->value, $results[2]->averageRating, 0.0001);
    }

    public function testFetchYearlyActivityStatsWithExistingDataset(): void
    {
        $user = DashboardStory::getDashboardUser();
        $currentYear = (int) new \DateTimeImmutable()->format('Y');
        $previousYear = $currentYear - 1;
        $periodStart = new \DateTimeImmutable(sprintf('%d-01-01 00:00:00', $previousYear));
        $periodEnd = new \DateTimeImmutable(sprintf('%d-12-31 23:59:59', $currentYear))->modify('+1 second');

        $results = $this->repository->fetchYearlyActivityStats($user, $periodStart, $periodEnd);

        $this->assertCount(2, $results);
        $this->assertSame(sprintf('%d-01-01', $previousYear), $results[0]->yearStart->format('Y-m-d'));
        $this->assertSame(1, $results[0]->sessionsCount);
        $this->assertSame(1, $results[0]->tripsCount);

        $this->assertSame(sprintf('%d-01-01', $currentYear), $results[1]->yearStart->format('Y-m-d'));
        $this->assertSame(3, $results[1]->sessionsCount);
        $this->assertSame(1, $results[1]->tripsCount);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->repository = null;
    }
}
