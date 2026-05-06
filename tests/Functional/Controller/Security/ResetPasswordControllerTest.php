<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Security;

use App\Controller\Security\ResetPasswordController;
use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Repository\UserRepository;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 */
#[CoversClass(ResetPasswordController::class)]
#[Medium]
final class ResetPasswordControllerTest extends CustomWebTestCase
{
    private const string PATH_RESET_PASSWORD = '/en/reset-password';
    private const string PATH_RESET_PASSWORD_RESET = '/en/reset-password/reset';
    private const string PATH_INDEX_TRIP = '/en/trips';

    private const string SEND_EMAIL_SUBMIT_BUTTON = 'Send password reset email';
    private const string FORM_EMAIL_FIELD = 'reset_password_request_form[email]';

    private const string RESET_PASSWORD_SUBMIT_BUTTON = 'Reset password';
    private const string FORM_PASSWORD_FIRST_FIELD = 'change_password_form[plainPassword][first]';
    private const string FORM_PASSWORD_SECOND_FIELD = 'change_password_form[plainPassword][second]';
    private const string NEW_VALID_PASSWORD = 'newValidPassword123';
    private const string NEW_WRONG_PASSWORD = 'newWrongPassword123';
    private const string ERROR_MESSAGE_INVALID_LINK = 'The reset password link is invalid';

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class, followRedirects: false);

        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testResetPasswordWithValidToken(): void
    {
        $this->goToResetPasswordForm(UserStory::getJohnUser());

        $this->client->submitForm(self::RESET_PASSWORD_SUBMIT_BUTTON, [
            self::FORM_PASSWORD_FIRST_FIELD => self::NEW_VALID_PASSWORD,
            self::FORM_PASSWORD_SECOND_FIELD => self::NEW_VALID_PASSWORD,
        ]);

        $this->assertResponseRedirects(self::PATH_INDEX_TRIP);

        $updatedUser = $this->userRepository->findOneByEmail(Email::from(UserStory::JOHN_EMAIL));
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertTrue($passwordHasher->isPasswordValid($updatedUser, self::NEW_VALID_PASSWORD));
    }

    public function testResetPasswordWithInvalidToken(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH_RESET_PASSWORD_RESET.'/%s', 'invalid-token')
        );

        $this->client->followRedirect();

        $this->assertResponseRedirects(self::PATH_RESET_PASSWORD);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.app-alert', self::ERROR_MESSAGE_INVALID_LINK);
    }

    public function testResetPasswordWithMismatchedPasswordsFails(): void
    {
        $this->goToResetPasswordForm(UserStory::getJohnUser());

        $this->client->submitForm(self::RESET_PASSWORD_SUBMIT_BUTTON, [
            self::FORM_PASSWORD_FIRST_FIELD => self::NEW_VALID_PASSWORD,
            self::FORM_PASSWORD_SECOND_FIELD => self::NEW_WRONG_PASSWORD,
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testAccessResetPageWithoutTokenIsFailed(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::PATH_RESET_PASSWORD_RESET
        );

        $this->assertResponseStatusCodeSame(404);
    }

    private function goToResetPasswordForm(User $user): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::PATH_RESET_PASSWORD
        );

        $this->assertResponseIsSuccessful();

        $this->client->submitForm(self::SEND_EMAIL_SUBMIT_BUTTON, [
            self::FORM_EMAIL_FIELD => $user->email->value,
        ]);

        $this->assertEmailCount(1);

        $message = $this->getMailerMessage();
        $decodedMessage = quoted_printable_decode($message->toString());

        preg_match('/https?:\/\/[^\s"<>()]+/i', $decodedMessage, $matches);

        $this->assertNotEmpty($matches, 'Reset link not found in email');

        $resetLink = $matches[0];
        $resetPath = parse_url($resetLink, PHP_URL_PATH);

        $this->assertIsString($resetPath, 'Failed to parse reset link from email');

        $this->client->request(
            Request::METHOD_GET,
            $resetPath
        );

        $this->assertResponseRedirects(self::PATH_RESET_PASSWORD_RESET);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
    }
}
