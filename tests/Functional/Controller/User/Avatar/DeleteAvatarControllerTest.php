<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User\Avatar;

use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[Medium]
final class DeleteAvatarControllerTest extends CustomWebTestCase
{
    use AvatarTestTrait;

    private const string PATH_PROFILE_EDIT = '/en/profile/edit';
    private const string PATH_AVATAR_DELETE = '/en/profile/avatar/delete';
    private const string SUBMIT_BUTTON = 'Delete';

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class, UserStory::JOHN_EMAIL);
    }

    public function testDeleteAvatarIsSuccessful(): void
    {
        $currentUser = $this->getCurrentUser();

        $this->assertNull($currentUser->avatarPath);

        $this->uploadAvatar();

        $currentUser = $this->getCurrentUser();

        $this->assertNotNull($currentUser->avatarPath);

        $this->client->request(Request::METHOD_GET, self::PATH_PROFILE_EDIT);
        $this->client->submitForm(self::SUBMIT_BUTTON);

        $currentUser = $this->getCurrentUser();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.app-avatar-fallback');
        $this->assertSelectorTextContains('.app-avatar-fallback', 'JD');
        $this->assertNull($currentUser->avatarPath);
    }

    public function testDeleteAvatarRequiresValidCsrfToken(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            self::PATH_AVATAR_DELETE,
            [
                '_token' => 'invalid_token',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteAvatarWithoutCsrfTokenIsRejected(): void
    {
        $this->client->request(Request::METHOD_POST, self::PATH_AVATAR_DELETE);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
