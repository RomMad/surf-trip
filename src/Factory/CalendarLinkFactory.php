<?php

declare(strict_types=1);

namespace App\Factory;

use App\Calendar\CalendarLinkableInterface;
use Spatie\CalendarLinks\Link;

final readonly class CalendarLinkFactory
{
    public function fromEvent(CalendarLinkableInterface $event): Link
    {
        return Link::create(
            title: $event->getCalendarTitle(),
            from: $event->getCalendarStartAt(),
            to: $event->getCalendarEndAt(),
        )
            ->description($event->getCalendarDescription() ?? '')
            ->address($event->getCalendarAddress() ?? '')
        ;
    }
}
