<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User\Avatar;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Repository\UserRepository;
use App\Tests\Fixtures\UserStory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

trait AvatarTestTrait
{
    private const string PATH_AVATAR = '/en/profile/avatar';
    private const string FORM_SELECTOR = 'form[name="avatar"]';
    private const string AVATAR_FILENAME = 'adventurer-1.png';

    private function getCurrentUser(): User
    {
        $userRepository = $this->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneByEmail(Email::from(UserStory::JOHN_EMAIL));

        $this->assertInstanceOf(User::class, $user);

        return $user;
    }

    private function uploadAvatar(?UploadedFile $file = null): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH_AVATAR);

        $this->assertResponseIsSuccessful();

        // Submit button is outside the form, so we need to get the form and submit it manually
        $form = $this->client->getCrawler()->filter(self::FORM_SELECTOR)->form();

        $this->client->submit($form, [
            'avatar[avatar]' => $file ?? $this->createAvatarUpload(),
        ]);
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
}
