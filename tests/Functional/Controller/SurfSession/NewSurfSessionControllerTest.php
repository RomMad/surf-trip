<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\SurfSession;

use App\Entity\SurfSession;
use App\Entity\ValueObject\Title;
use App\Enum\SurfSession\SurfSessionRating;
use App\Factory\TripFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\TripStory;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Medium]
final class NewSurfSessionControllerTest extends CustomWebTestCase
{
    // Paths
    private const string PATH_INDEX = '/en/sessions';
    private const string PATH_NEW = '/en/sessions/new';
    private const string PATH_NEW_FROM_TRIP = '/en/trip/%d/sessions/new';
    // Selectors
    private const string FORM = 'form[name="surf_session"]';
    private const string FIRST_CARD = '.app-card';
    // Labels
    private const string TITLE = 'New session';
    private const string NEW_SESSION_LINK = 'Create new';
    private const string SUBMIT_BUTTON = 'Save';
    // Messages
    private const string MESSAGE_SUCCESS = 'The session has been created.';
    // Data
    private const string SESSION_SPOT = 'Tenerife';
    private const string SESSION_BOARD = 'Firewire 5.10';
    private const string SESSION_OBJECTIVE = 'Practice barrel rolls';
    private const string SESSION_COMMENT = 'Amazing conditions';

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);
    }

    public function testNewSurfSessionPageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH_INDEX);
        $this->client->clickLink(self::NEW_SESSION_LINK);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists(self::FORM);
    }

    public function testCreateSurfSessionIsSuccessful(): void
    {
        $trip = TripFactory::find(['title' => Title::from(TripStory::CURRENT_TRIP_TITLE)]);

        $this->client->request(Request::METHOD_GET, self::PATH_NEW);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            'surf_session' => [
                'spot' => self::SESSION_SPOT,
                'board' => self::SESSION_BOARD,
                'startAt' => new \DateTimeImmutable('-1 day 15:00')->format(self::FORMAT_DATETIME),
                'endAt' => new \DateTimeImmutable('-1 day 17:00')->format(self::FORMAT_DATETIME),
                'trip' => $trip->id,
                'rating' => SurfSessionRating::Good->value,
                'objective' => self::SESSION_OBJECTIVE,
                'comment' => self::SESSION_COMMENT,
            ],
        ]);

        $createdSession = $this->getRepository(SurfSession::class)->findOneBy(['spot' => self::SESSION_SPOT]);

        $this->assertInstanceOf(SurfSession::class, $createdSession);
        $this->assertSame(self::SESSION_SPOT, $createdSession->spot);
        $this->assertSame(self::SESSION_BOARD, $createdSession->board);
        $this->assertNotNull($createdSession->trip);
        $this->assertSame($trip->id, $createdSession->trip->id);
        $this->assertSame(SurfSessionRating::Good, $createdSession->rating);

        $this->assertResponseIsSuccessful();
        $this->assertAlertSuccessExists();
        $this->assertSelectorTextContains(self::ALERT_SUCCESS, self::MESSAGE_SUCCESS);
        $this->assertSelectorTextContains(self::FIRST_CARD, self::SESSION_SPOT);
        $this->assertSelectorTextContains(self::FIRST_CARD, self::SESSION_BOARD);
    }

    public function testNewSurfSessionWithTrip(): void
    {
        $trip = TripFactory::find(['title' => Title::from(TripStory::CURRENT_TRIP_TITLE)]);
        $now = new \DateTimeImmutable()->setTime(10, 0);

        $this->client->request(Request::METHOD_GET, sprintf(self::PATH_NEW_FROM_TRIP, $trip->id));

        $startAt = $this->parseDateTimeFromInput('#surf_session_startAt');
        $endAt = $this->parseDateTimeFromInput('#surf_session_endAt');

        $this->assertResponseIsSuccessful();
        $this->assertSame($trip->location->value, $this->getInputValue('#surf_session_spot'));
        $this->assertSame((string) $trip->id, $this->getInputValue('#surf_session_trip option[selected]'));
        $this->assertSame($now->format('Y-m-d\TH'), $startAt->format('Y-m-d\TH'));
        $this->assertSame($startAt->modify('+2 hours')->format('Y-m-d\TH'), $endAt->format('Y-m-d\TH'));
    }

    private function parseDateTimeFromInput(string $selector): \DateTimeImmutable
    {
        $value = $this->getInputValue($selector);

        return \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $value);
    }

    private function getInputValue(string $selector): string
    {
        return (string) $this->client->getCrawler()->filter($selector)->attr('value');
    }
}
