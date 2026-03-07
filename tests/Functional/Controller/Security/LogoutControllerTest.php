<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Security;

use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
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
