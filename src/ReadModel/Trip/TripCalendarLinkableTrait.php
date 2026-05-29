<?php

declare(strict_types=1);

namespace App\ReadModel\Trip;

use App\Entity\ValueObject\Location;
use App\Entity\ValueObject\Title;

/**
 * @property Title              $title
 * @property \DateTimeImmutable $startAt
 * @property \DateTimeImmutable $endAt
 * @property ?string            $description
 * @property Location           $location
 */
trait TripCalendarLinkableTrait
{
    public function getCalendarTitle(): string
    {
        return $this->title->value;
    }

    public function getCalendarStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function getCalendarEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function getCalendarDescription(): ?string
    {
        return $this->description;
    }

    public function getCalendarAddress(): ?string
    {
        return $this->location->value;
    }
}
