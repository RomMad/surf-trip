<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Controller\User\EditProfileController;
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
#[CoversClass(EditProfileController::class)]
#[Medium]
final class EditProfileControllerTest extends CustomWebTestCase
{
    private const string PATH = '/en/profile/edit';
    private const string TITLE = 'Edit profile';
    private const string FORM = 'form[name="profile"]';
    private const string SUBMIT_BUTTON = 'Update';
    private const string MESSAGE_SUCCESS = 'Your profile has been updated.';
    private const string UPDATED_EMAIL = 'john.updated@test.com';
    private const string UPDATED_USERNAME = 'john.updated';
    private const string UPDATED_FIRST_NAME = 'Johnny';
    private const string UPDATED_LAST_NAME = 'WAVE';
    private const string UPDATED_LOCATION = 'Biarritz';
    private const string UPDATED_INSTAGRAM = '@johnny';
    private const string UPDATED_DESCRIPTION = 'Regular and goofy rider.';

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class, UserStory::JOHN_EMAIL);

        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }

    public function testEditProfilePageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
        $this->assertSelectorExists(self::FORM);
    }

    public function testEditProfileIsSuccessful(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            'profile' => [
                'email' => self::UPDATED_EMAIL,
                'username' => self::UPDATED_USERNAME,
                'firstName' => self::UPDATED_FIRST_NAME,
                'lastName' => self::UPDATED_LAST_NAME,
                'location' => self::UPDATED_LOCATION,
                'instagram' => self::UPDATED_INSTAGRAM,
                'description' => self::UPDATED_DESCRIPTION,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertAlertSuccessExists();
        $this->assertSelectorTextContains(self::ALERT_SUCCESS, self::MESSAGE_SUCCESS);

        $user = $this->userRepository->findOneByEmail(Email::from(self::UPDATED_EMAIL));

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(self::UPDATED_USERNAME, (string) $user->username);
        $this->assertSame(self::UPDATED_FIRST_NAME, (string) $user->firstName);
        $this->assertSame(self::UPDATED_LAST_NAME, (string) $user->lastName);
        $this->assertSame(self::UPDATED_LOCATION, $user->location);
        $this->assertSame(self::UPDATED_INSTAGRAM, $user->instagram);
        $this->assertSame(self::UPDATED_DESCRIPTION, $user->description);
    }
}
