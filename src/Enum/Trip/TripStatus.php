<?php

declare(strict_types=1);

namespace App\Enum\Trip;

enum TripStatus: string
{
    case Upcoming = 'upcoming';
    case InProgress = 'in_progress';
    case Finished = 'finished';

    public static function fromPeriod(
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
    ): self {
        $now = new \DateTimeImmutable();

        return match (true) {
            $now < $startAt => self::Upcoming,
            $now > $endAt => self::Finished,
            default => self::InProgress,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Upcoming => 'trip.status.upcoming.label',
            self::Finished => 'trip.status.finished.label',
            self::InProgress => 'trip.status.in_progress.label',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Upcoming => 'lightgreen',
            self::Finished => 'secondary',
            self::InProgress => 'green',
        };
    }
}
