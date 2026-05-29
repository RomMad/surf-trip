<?php

declare(strict_types=1);

namespace App\ReadModel\Calendar;

interface CalendarLinkableInterface
{
    public function getCalendarTitle(): string;

    public function getCalendarStartAt(): \DateTimeImmutable;

    public function getCalendarEndAt(): \DateTimeImmutable;

    public function getCalendarDescription(): ?string;

    public function getCalendarAddress(): ?string;
}
