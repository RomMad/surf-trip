<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Security;

use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Medium]
final class RequestResetPasswordControllerTest extends CustomWebTestCase
{
    private const string PATH_RESET_PASSWORD = '/en/reset-password';
    private const string PATH_CHECK_EMAIL = '/en/reset-password/check-email';
    private const string TITLE_RESET_PASSWORD = 'Reset your password';
    private const string EMAIL_SUBJECT = 'Your password reset request';
    private const string TITLE_EMAIL_SENT = 'Password Reset Email Sent';
    private const string EXPIRATION_MESSAGE = 'This link will expire in 1 hour';
    private const string SUBMIT_BUTTON = 'Send password reset email';
    private const string FORM_EMAIL_FIELD = 'reset_password_request_form[email]';

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class, followRedirects: false);
    }

    public function testRequestResetPasswordPageIsDisplayedSuccessfully(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::PATH_RESET_PASSWORD
        );

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains(self::TITLE_RESET_PASSWORD);
    }

    public function testSubmittingValidEmailSendsEmailAndRedirects(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::PATH_RESET_PASSWORD
        );

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            self::FORM_EMAIL_FIELD => UserStory::JOHN_EMAIL,
        ]);

        $email = $this->getMailerMessage();

        $this->assertEmailCount(1);
        $this->assertEmailAddressContains($email, 'to', UserStory::JOHN_EMAIL);
        $this->assertEmailSubjectContains($email, self::EMAIL_SUBJECT);
        $this->assertResponseRedirects(self::PATH_CHECK_EMAIL);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains(self::TITLE_EMAIL_SENT);
        $this->assertSelectorTextContains('p', self::EXPIRATION_MESSAGE);
    }

    public function testSubmittingNonexistentEmailDoesNotRevealUser(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::PATH_RESET_PASSWORD
        );

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            self::FORM_EMAIL_FIELD => 'nonexistent@example.com',
        ]);

        $this->assertResponseRedirects(self::PATH_CHECK_EMAIL);
        $this->assertEmailCount(0);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains(self::TITLE_EMAIL_SENT);
    }
}
