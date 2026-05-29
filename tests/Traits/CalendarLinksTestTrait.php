<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Calendar\CalendarLinkableInterface;
use App\Factory\CalendarLinkFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @phpstan-require-extends \App\Tests\CustomWebTestCase
 */
trait CalendarLinksTestTrait
{
    protected const string GOOGLE_LABEL = 'Google Calendar';
    protected const string OUTLOOK_LABEL = 'Outlook.com';
    protected const string OFFICE_LABEL = 'Outlook office';
    protected const string ICAL_LABEL = 'iCalendar';

    private function assertCalendarLinksCanBeClicked(CalendarLinkableInterface $event, string $path): void
    {
        $calendarLinkFactory = $this->getContainer()->get(CalendarLinkFactory::class);
        $calendarLink = $calendarLinkFactory->fromEvent($event);

        foreach ([
            self::GOOGLE_LABEL => $calendarLink->google(),
            self::OUTLOOK_LABEL => $calendarLink->webOutlook(),
            self::OFFICE_LABEL => $calendarLink->webOffice(),
            self::ICAL_LABEL => $calendarLink->ics(),
        ] as $label => $expectedUri) {
            $this->client->request(Request::METHOD_GET, $path);

            $link = $this->client->getCrawler()->selectLink($label)->link();

            $this->assertResponseIsSuccessful();
            $this->assertSame($expectedUri, $link->getUri());

            $this->client->click($link);
        }
    }
}
