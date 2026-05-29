<?php

declare(strict_types=1);

namespace App\Entity\Traits;

/**
 * @property ?string             $spot
 * @property ?\DateTimeImmutable $startAt
 * @property ?\DateTimeImmutable $endAt
 * @property ?string             $objective
 * @property ?string             $comment
 */
trait SurfSessionCalendarLinkableTrait
{
    public function getCalendarTitle(): string
    {
        return $this->spot;
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
        if ('' === $this->objective) {
            return $this->comment;
        }

        return sprintf("%s\n\n%s", $this->objective, $this->comment);
    }

    public function getCalendarAddress(): ?string
    {
        return $this->spot;
    }
}
