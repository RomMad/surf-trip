<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Security;

use App\Tests\CustomWebTestCase;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Medium]
final class CheckEmailResetPasswordControllerTest extends CustomWebTestCase
{
    private const string CHECK_EMAIL_PATH = '/en/reset-password/check-email';
    private const string TITLE = 'Password Reset Email Sent';
    private const string EXPIRATION_MESSAGE = 'This link will expire in 1 hour';

    protected function setUp(): void
    {
        $this->setUpTest();
    }

    public function testCheckEmailPageDisplaysSuccessfully(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::CHECK_EMAIL_PATH
        );

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains(self::TITLE);
        $this->assertSelectorTextContains('p', self::EXPIRATION_MESSAGE);
    }
}
