<?php

declare(strict_types=1);

namespace App\Service\SurfSession;

use App\Form\Model\SurfSession\SurfSessionWriteModel;
use App\ReadModel\Trip\AbstractTripReadModel;
use App\ReadModel\Trip\TripSelectReadModel;

final class SurfSessionWriteModelFactory
{
    private const int DEFAULT_SESSION_DURATION_HOURS = 2;

    public function create(?AbstractTripReadModel $trip = null): SurfSessionWriteModel
    {
        $surfSessionWriteModel = new SurfSessionWriteModel();
        $startAt = $this->resolveStartAt($trip);

        if (null !== $trip) {
            $trip = new TripSelectReadModel(
                id: $trip->id,
                title: $trip->title->value,
                location: $trip->location->value,
            );
        }

        $surfSessionWriteModel->trip = $trip;
        $surfSessionWriteModel->spot = $trip?->location;
        $surfSessionWriteModel->startAt = $startAt;
        $surfSessionWriteModel->endAt = $startAt->modify(sprintf('+%d hours', self::DEFAULT_SESSION_DURATION_HOURS));

        return $surfSessionWriteModel;
    }

    private function resolveStartAt(?AbstractTripReadModel $trip): \DateTimeImmutable
    {
        $now = new \DateTimeImmutable();

        $startAt = match (true) {
            null === $trip => $now,
            $now < $trip->startAt => $trip->startAt,
            $now > $trip->endAt => $trip->endAt,
            default => $now,
        };

        return $startAt->setTime(10, 0);
    }
}
