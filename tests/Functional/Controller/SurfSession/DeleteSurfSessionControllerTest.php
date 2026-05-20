<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\SurfSession;

use App\Controller\SurfSession\DeleteSurfSessionController;
use App\Entity\SurfSession;
use App\Factory\SurfSessionFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(DeleteSurfSessionController::class)]
#[Medium]
final class DeleteSurfSessionControllerTest extends CustomWebTestCase
{
    private const string PATH_EDIT = '/en/sessions/%d/edit';
    private const string DELETE_BUTTON = 'Delete';
    private const string MESSAGE_SUCCESS = 'The session has been deleted.';

    private ?SurfSession $surfSession = null;

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);

        $this->surfSession = SurfSessionFactory::last();
    }

    public function testDeleteSurfSessionIsSuccessful(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH_EDIT, $this->surfSession->id)
        );

        $this->assertResponseIsSuccessful();

        $this->client->submitForm(self::DELETE_BUTTON);

        $surfSession = $this->getRepository(SurfSession::class)->find($this->surfSession->id);

        $this->assertResponseIsSuccessful();
        $this->assertAlertSuccessExists();
        $this->assertSelectorTextContains(self::ALERT_SUCCESS, self::MESSAGE_SUCCESS);
        $this->assertNull($surfSession);
    }
}
