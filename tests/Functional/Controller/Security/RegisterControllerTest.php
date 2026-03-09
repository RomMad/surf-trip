<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Security;

use App\Controller\Security\RegisterController;
use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Repository\UserRepository;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(RegisterController::class)]
#[Medium]
final class RegisterControllerTest extends CustomWebTestCase
{
    private const string PATH = '/en/register';
    private const string TITLE = 'Register';
    private const string SUBMIT_BUTTON = 'Register';
    private const string LOGOUT_LINK = 'Log out';
    private const string USER_EMAIL = 'new.user@test.com';
    private const string USER_FIRST_NAME = 'New';
    private const string USER_LAST_NAME = 'User';
    private const string USER_PASSWORD = 'Password123!';

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class);
    }

    public function testRegisterPageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
    }

    public function testRegisterWithValidData(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            'registration_form' => [
                'email' => self::USER_EMAIL,
                'firstName' => self::USER_FIRST_NAME,
                'lastName' => self::USER_LAST_NAME,
                'plainPassword' => [
                    'first' => self::USER_PASSWORD,
                    'second' => self::USER_PASSWORD,
                ],
                'agreeTerms' => true,
            ],
        ]);

        /** @var UserRepository $userRepository */
        $userRepository = $this->getRepository(User::class);
        $user = $userRepository->findOneByEmail(Email::from(self::USER_EMAIL));

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', self::LOGOUT_LINK);
        $this->assertInstanceOf(User::class, $user);
    }
}
