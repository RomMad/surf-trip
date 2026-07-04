<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Repository\UserRepository;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[Medium]
final class AvatarControllerTest extends CustomWebTestCase
{
    private const string PATH = '/en/profile/avatar';
    private const string FORM_SELECTOR = 'form[name="avatar"]';
    private const string AVATAR_FILENAME = 'adventurer-1.png';
    private const string MESSAGE_ERROR = 'The mime type of the file is invalid';

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class, UserStory::JOHN_EMAIL);

        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }

    public function testAvatarFormIsDisplayedOnGet(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists(self::FORM_SELECTOR);
        $this->assertSelectorExists('input[type="file"]');
    }

    public function testAvatarUploadIsSuccessful(): void
    {
        $currentUser = $this->userRepository->findOneByEmail(Email::from(UserStory::JOHN_EMAIL));

        $this->assertNull($currentUser->avatarPath);

        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();

        $form = $this->client->getCrawler()->filter(self::FORM_SELECTOR)->form();

        $this->client->submit($form, [
            'avatar[avatar]' => $this->createAvatarUpload(),
        ]);

        $user = $this->userRepository->findOneByEmail(Email::from(UserStory::JOHN_EMAIL));

        $this->assertResponseIsSuccessful();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->avatarPath);
        $this->assertStringEndsWith('.webp', $user->avatarPath);
    }

    public function testAvatarUploadWithInvalidFileDisplaysForm(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);
        $this->assertResponseIsSuccessful();

        $form = $this->client->getCrawler()->filter(self::FORM_SELECTOR)->form();

        $this->client->submit($form, [
            'avatar[avatar]' => $this->createInvalidFileUpload(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSelectorExists(self::FORM_SELECTOR);
        $this->assertSelectorTextContains('.app-errors', self::MESSAGE_ERROR);
    }

    private function createAvatarUpload(): UploadedFile
    {
        $projectDir = $this->getParameter('kernel.project_dir');

        return new UploadedFile(
            path: sprintf('%s/fixtures/avatars/%s', $projectDir, self::AVATAR_FILENAME),
            originalName: self::AVATAR_FILENAME,
            test: true,
        );
    }

    private function createInvalidFileUpload(): UploadedFile
    {
        return new UploadedFile(
            path: __FILE__,
            originalName: 'test.txt',
            test: true,
        );
    }
}
