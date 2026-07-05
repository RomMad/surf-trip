<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User\Avatar;

use App\Entity\User;
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
final class SetAvatarControllerTest extends CustomWebTestCase
{
    use AvatarTestTrait;

    private const string PATH = '/en/profile/avatar';
    private const string MESSAGE_ERROR = 'The mime type of the file is invalid';

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class, UserStory::JOHN_EMAIL);
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
        $currentUser = $this->getCurrentUser();

        $this->assertNull($currentUser->avatarPath);

        $this->uploadAvatar();

        $user = $this->getCurrentUser();

        $this->assertResponseIsSuccessful();
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->avatarPath);
        $this->assertStringEndsWith('.webp', $user->avatarPath);
    }

    public function testAvatarUploadWithInvalidFileDisplaysForm(): void
    {
        $invalidFile = $this->createInvalidFileUpload();
        $this->uploadAvatar($invalidFile);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSelectorExists(self::FORM_SELECTOR);
        $this->assertSelectorTextContains('.app-errors', self::MESSAGE_ERROR);
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
