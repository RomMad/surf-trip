<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\SurfSession;

use App\Entity\SurfSession;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\Title;
use App\Enum\SurfSession\SurfSessionRating;
use App\Factory\SurfSessionFactory;
use App\Factory\TripFactory;
use App\Factory\UserFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\TripStory;
use App\Tests\Fixtures\UserStory;
use App\Tests\Traits\CalendarLinksTestTrait;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Medium]
final class EditSurfSessionControllerTest extends CustomWebTestCase
{
    use CalendarLinksTestTrait;

    // Paths
    private const string PATH_EDIT = '/en/sessions/%d/edit';
    // Selectors
    private const string FORM = 'form[name="surf_session"]';
    private const string CARD_ID = '#surf_session_%d';
    // Labels
    private const string TITLE = 'Edit session';
    private const string SUBMIT_BUTTON = 'Update';
    // Messages
    private const string MESSAGE_SUCCESS = 'The session has been updated.';
    // Data
    private const string UPDATED_SESSION_SPOT = 'Malibu';
    private const string UPDATED_SESSION_BOARD = 'Rusty 6.0';
    private const string UPDATED_SESSION_OBJECTIVE = 'Update objective';
    private const string UPDATED_SESSION_COMMENT = 'Update comment';

    private ?SurfSession $surfSession = null;

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);

        $this->surfSession = SurfSessionFactory::last();
    }

    public function testEditSurfSessionForbiddenForOtherUser(): void
    {
        $otherUser = UserFactory::find(['email' => Email::from(UserStory::JANE_EMAIL)]);
        $this->client->loginUser($otherUser);

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH_EDIT, $this->surfSession->id)
        );

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditSurfSessionPageIsDisplayed(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH_EDIT, $this->surfSession->id)
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
        $this->assertSelectorExists(self::FORM);
    }

    public function testEditSurfSessionIsSuccessful(): void
    {
        $trip = TripFactory::find(['title' => Title::from(TripStory::CURRENT_TRIP_TITLE)]);

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH_EDIT, $this->surfSession->id)
        );

        $this->assertResponseIsSuccessful();

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            'surf_session' => [
                'spot' => self::UPDATED_SESSION_SPOT,
                'board' => self::UPDATED_SESSION_BOARD,
                'startAt' => new \DateTimeImmutable('+1 days 10:00')->format(self::FORMAT_DATETIME),
                'endAt' => new \DateTimeImmutable('+1 days 12:00')->format(self::FORMAT_DATETIME),
                'trip' => $trip->id,
                'rating' => SurfSessionRating::Excellent->value,
                'objective' => self::UPDATED_SESSION_OBJECTIVE,
                'comment' => self::UPDATED_SESSION_COMMENT,
            ],
        ]);

        $updatedSession = $this->getRepository(SurfSession::class)->find($this->surfSession->id);
        $cardSelector = sprintf(self::CARD_ID, $this->surfSession->id);

        $this->assertInstanceOf(SurfSession::class, $updatedSession);
        $this->assertSame(self::UPDATED_SESSION_SPOT, $updatedSession->spot);
        $this->assertSame(self::UPDATED_SESSION_BOARD, $updatedSession->board);
        $this->assertSame($trip->id, $updatedSession->trip?->id);
        $this->assertSame(SurfSessionRating::Excellent, $updatedSession->rating);

        $this->assertResponseIsSuccessful();
        $this->assertAlertSuccessExists();
        $this->assertSelectorTextContains(self::ALERT_SUCCESS, self::MESSAGE_SUCCESS);
        $this->assertSelectorTextContains($cardSelector, self::UPDATED_SESSION_SPOT);
        $this->assertSelectorTextContains($cardSelector, self::UPDATED_SESSION_BOARD);
    }

    public function testCalendarLinksCanBeClickedFromTheEditPage(): void
    {
        $this->assertCalendarLinksCanBeClicked(
            $this->surfSession,
            sprintf(self::PATH_EDIT, $this->surfSession->id)
        );
    }
}
