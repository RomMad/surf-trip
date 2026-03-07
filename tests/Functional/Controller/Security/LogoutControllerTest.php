<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Security;

use App\Controller\Security\LogoutController;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(LogoutController::class)]
#[Medium]
final class LogoutControllerTest extends CustomWebTestCase
{
    private const string PATH = '/logout';

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class);
    }

    public function testLoginPageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
    }
}
