<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Security;

use App\Controller\Security\LoginController;
use App\Factory\UserFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(LoginController::class)]
#[Medium]
final class LoginControllerTest extends CustomWebTestCase
{
    private const string PATH = '/login';
    private const string TITLE = 'Log in';
    private const string SUBMIT_BUTTON = 'Log in';
    private const string LOGOUT_LINK = 'Log out';

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class);
    }

    public function testLoginPageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
    }

    public function testLoginPageIsRedirectedWhenUserIsAlreadyAuthenticated(): void
    {
        $this->client->followRedirects(true);

        $this->client->loginUser(UserFactory::first());
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertSelectorTextNotContains(self::TITLE_H1, self::TITLE);
        $this->assertSelectorTextSame(self::TITLE_H1, 'Trips');
    }

    public function testLoginWithValidCredentials(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            'email' => UserStory::JOHN_EMAIL,
            'password' => UserStory::JOHN_PASSWORD,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', self::LOGOUT_LINK);
    }
}
