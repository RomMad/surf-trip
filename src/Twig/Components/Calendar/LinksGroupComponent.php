<?php

declare(strict_types=1);

namespace App\Twig\Components\Calendar;

use App\Calendar\CalendarLinkableInterface;
use App\Factory\CalendarLinkFactory;
use Spatie\CalendarLinks\Link;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Calendar:LinksGroup')]
final class LinksGroupComponent
{
    public Link $link;

    public function __construct(
        private readonly CalendarLinkFactory $calendarLinkFactory,
    ) {}

    public function mount(CalendarLinkableInterface $event): void
    {
        $this->link = $this->calendarLinkFactory->fromEvent($event);
    }
}
