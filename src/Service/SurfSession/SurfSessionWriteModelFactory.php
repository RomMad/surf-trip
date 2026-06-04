<?php

declare(strict_types=1);

namespace App\Service\SurfSession;

use App\Enum\SurfSession\SurfSessionDuration;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use App\ReadModel\Trip\AbstractTripReadModel;
use App\ReadModel\Trip\TripSelectReadModel;

final class SurfSessionWriteModelFactory
{
    public function create(?AbstractTripReadModel $trip = null): SurfSessionWriteModel
    {
        $surfSessionWriteModel = new SurfSessionWriteModel();
        $startAt = $this->resolveStartAt($trip);
        $tripSelect = null;

        if (null !== $trip) {
            $tripSelect = new TripSelectReadModel(
                id: $trip->id,
                title: $trip->title->value,
                location: $trip->location->value,
            );
        }

        $surfSessionWriteModel->trip = $tripSelect;
        $surfSessionWriteModel->spot = $tripSelect?->location;
        $surfSessionWriteModel->date = $startAt->setTime(0, 0);
        $surfSessionWriteModel->startTime = $startAt->format('H:i');
        $surfSessionWriteModel->durationMinutes = SurfSessionDuration::Minutes120;

        return $surfSessionWriteModel;
    }

    private function resolveStartAt(?AbstractTripReadModel $trip = null): \DateTimeImmutable
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
