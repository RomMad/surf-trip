<?php

declare(strict_types=1);

namespace App\Service\Image;

use App\Entity\User;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final readonly class AvatarManager
{
    private const string FILE_EXTENSION = 'webp';

    public function __construct(
        private ImageProcessor $imageProcessor,
        #[Target('avatars.storage')]
        private FilesystemOperator $storage,
    ) {}

    public function upload(User $user, ?UploadedFile $file = null): void
    {
        if (null === $file) {
            return;
        }

        $avatarPath = $this->getAvatarPath($user);
        $avatar = $this->imageProcessor->createAvatar($file);

        if (is_resource($avatar)) {
            rewind($avatar);
        }

        $this->storage->writeStream(
            $avatarPath,
            $avatar,
        );

        if (is_resource($avatar)) {
            fclose($avatar);
        }

        $this->removeImage($user->avatarPath);
        $user->avatarPath = $avatarPath;
    }

    private function removeImage(?string $imagePath = null): void
    {
        if (null === $imagePath) {
            return;
        }

        if ($this->storage->fileExists($imagePath)) {
            $this->storage->delete($imagePath);
        }
    }

    private function getAvatarPath(User $user): string
    {
        return sprintf('%s/%s.%s', $user->id, Uuid::v7(), self::FILE_EXTENSION);
    }
}
