<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\SurfSession;

use App\Controller\SurfSession\EditSurfSessionController;
use App\Entity\SurfSession;
use App\Enum\SurfSession\SurfSessionRating;
use App\Factory\SurfSessionFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(EditSurfSessionController::class)]
#[Medium]
final class EditSurfSessionControllerTest extends CustomWebTestCase
{
    // Paths
    private const string PATH_EDIT = '/en/sessions/%d/edit';
    // Selectors
    private const string FORM = 'form[name="surf_session"]';
    private const string CARD_ID = '#surf_session_%d';
    // Labels
    private const string TITLE = 'Edit session';
    private const string SUBMIT_BUTTON = 'Save';
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
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH_EDIT, $this->surfSession->id)
        );

        $this->assertResponseIsSuccessful();

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            'surf_session' => [
                'spot' => self::UPDATED_SESSION_SPOT,
                'board' => self::UPDATED_SESSION_BOARD,
                'startAt' => new \DateTimeImmutable('+2 days 10:00')->format(self::FORMAT_DATETIME),
                'endAt' => new \DateTimeImmutable('+2 days 12:00')->format(self::FORMAT_DATETIME),
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
        $this->assertSame(SurfSessionRating::Excellent, $updatedSession->rating);

        $this->assertResponseIsSuccessful();
        $this->assertAlertSuccessExists();
        $this->assertSelectorTextContains(self::ALERT_SUCCESS, self::MESSAGE_SUCCESS);
        $this->assertSelectorTextContains($cardSelector, self::UPDATED_SESSION_SPOT);
        $this->assertSelectorTextContains($cardSelector, self::UPDATED_SESSION_BOARD);
    }
}
