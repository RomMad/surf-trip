<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Dashboard\DashboardKpisDto;
use App\Dto\Dashboard\MonthlySessionStatDto;
use App\Dto\Dashboard\SpotStatDto;
use App\Dto\Dashboard\YearlyActivityStatDto;
use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Types;

final readonly class DashboardStatisticsRepository
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function fetchKpis(User $user, \DateTimeImmutable $yearStart, \DateTimeImmutable $nextYearStart): DashboardKpisDto
    {
        $sql = <<<'SQL'
            WITH trip_stats AS (
                SELECT
                    COUNT(DISTINCT t.id) AS total_trips,
                    COUNT(DISTINCT t.id) FILTER (
                        WHERE t.start_at >= :year AND t.start_at < :next_year
                    ) AS trips_this_year
                FROM trip t
                INNER JOIN trip_user tu ON tu.trip_id = t.id
                WHERE tu.user_id = :user_id
            ),
            session_stats AS (
                SELECT
                    COUNT(*) AS total_sessions,
                    COUNT(*) FILTER (
                        WHERE s.start_at >= :year AND s.start_at < :next_year
                    ) AS sessions_this_year,
                    AVG(s.rating::numeric) AS average_session_rating
                FROM surf_session s
                WHERE s.user_id = :user_id
            )
            SELECT
                trip_stats.total_trips,
                trip_stats.trips_this_year,
                session_stats.total_sessions,
                session_stats.sessions_this_year,
                session_stats.average_session_rating
            FROM trip_stats
            CROSS JOIN session_stats
            SQL;

        $result = $this->connection->fetchAssociative(
            $sql,
            [
                'user_id' => $user->id,
                'year' => $yearStart,
                'next_year' => $nextYearStart,
            ],
            [
                'user_id' => ParameterType::INTEGER,
                'year' => Types::DATETIME_IMMUTABLE,
                'next_year' => Types::DATETIME_IMMUTABLE,
            ]
        );

        return new DashboardKpisDto(
            totalTrips: (int) ($result['total_trips'] ?? 0),
            tripsThisYear: (int) ($result['trips_this_year'] ?? 0),
            totalSessions: (int) ($result['total_sessions'] ?? 0),
            sessionsThisYear: (int) ($result['sessions_this_year'] ?? 0),
            averageSessionRating: isset($result['average_session_rating']) ? round((float) $result['average_session_rating'], 1) : null,
        );
    }

    /**
     * @return list<MonthlySessionStatDto>
     */
    public function fetchMonthlySessionStats(User $user, \DateTimeImmutable $periodStart, \DateTimeImmutable $periodEnd): array
    {
        $sql = <<<'SQL'
            SELECT
                DATE_TRUNC('month', s.start_at)::date AS month,
                COUNT(*) AS sessions_count
            FROM surf_session s
            WHERE s.user_id = :user_id
                AND s.start_at >= :period_start
                AND s.start_at < :period_end
            GROUP BY month
            ORDER BY month ASC
            SQL;

        $rows = $this->connection->fetchAllAssociative(
            $sql,
            [
                'user_id' => $user->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ],
            [
                'user_id' => ParameterType::INTEGER,
                'period_start' => Types::DATETIME_IMMUTABLE,
                'period_end' => Types::DATETIME_IMMUTABLE,
            ]
        );

        return array_map(
            static fn (array $row): MonthlySessionStatDto => new MonthlySessionStatDto(
                new \DateTimeImmutable((string) $row['month']),
                (int) $row['sessions_count'],
            ),
            $rows,
        );
    }

    /**
     * @return list<SpotStatDto>
     */
    public function fetchTopSpots(User $user, int $limit = 5): array
    {
        $sql = <<<'SQL'
            SELECT
                s.spot,
                COUNT(*) AS sessions_count,
                AVG(s.rating::numeric) AS average_rating
            FROM surf_session s
            WHERE s.user_id = :user_id
            GROUP BY s.spot
            ORDER BY sessions_count DESC, average_rating DESC NULLS LAST, s.spot ASC
            LIMIT :limit
            SQL;

        $rows = $this->connection->fetchAllAssociative(
            $sql,
            [
                'user_id' => $user->id,
                'limit' => $limit,
            ],
            [
                'user_id' => ParameterType::INTEGER,
                'limit' => ParameterType::INTEGER,
            ]
        );

        return array_map(
            static fn (array $row): SpotStatDto => new SpotStatDto(
                (string) $row['spot'],
                (int) $row['sessions_count'],
                null === $row['average_rating'] ? null : round((float) $row['average_rating'], 1),
            ),
            $rows,
        );
    }

    /**
     * @return list<YearlyActivityStatDto>
     */
    public function fetchYearlyActivityStats(
        User $user,
        \DateTimeImmutable $periodStart,
        \DateTimeImmutable $periodEnd,
    ): array {
        $sql = <<<'SQL'
            WITH session_years AS (
                SELECT
                    DATE_TRUNC('year', s.start_at)::date AS year,
                    COUNT(*) AS sessions_count
                FROM surf_session s
                WHERE s.user_id = :user_id
                    AND s.start_at >= :period_start
                    AND s.start_at < :period_end
                GROUP BY year
            ),
            trip_years AS (
                SELECT
                    DATE_TRUNC('year', t.start_at)::date AS year,
                    COUNT(DISTINCT t.id) AS trips_count
                FROM trip t
                INNER JOIN trip_user tu ON tu.trip_id = t.id
                WHERE tu.user_id = :user_id
                    AND t.start_at >= :period_start
                    AND t.start_at < :period_end
                GROUP BY year
            ),
            years AS (
                SELECT year FROM session_years
                UNION
                SELECT year FROM trip_years
            )
            SELECT
                years.year,
                COALESCE(session_years.sessions_count, 0) AS sessions_count,
                COALESCE(trip_years.trips_count, 0) AS trips_count
            FROM years
            LEFT JOIN session_years ON session_years.year = years.year
            LEFT JOIN trip_years ON trip_years.year = years.year
            ORDER BY years.year ASC
            SQL;

        $rows = $this->connection->fetchAllAssociative(
            $sql,
            [
                'user_id' => $user->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ],
            [
                'user_id' => ParameterType::INTEGER,
                'period_start' => Types::DATETIME_IMMUTABLE,
                'period_end' => Types::DATETIME_IMMUTABLE,
            ],
        );

        return array_map(
            static fn (array $row): YearlyActivityStatDto => new YearlyActivityStatDto(
                new \DateTimeImmutable((string) $row['year']),
                (int) $row['sessions_count'],
                (int) $row['trips_count'],
            ),
            $rows,
        );
    }
}
