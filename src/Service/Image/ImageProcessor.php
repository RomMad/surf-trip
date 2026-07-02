<?php

declare(strict_types=1);

namespace App\Service\Image;

use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImageProcessor
{
    private const int AVATAR_SIZE = 512;
    private const int  AVATAR_QUALITY = 65;

    private ImageManagerInterface $manager;

    public function __construct()
    {
        $this->manager = ImageManager::usingDriver(ImagickDriver::class);
    }

    /**
     * @return resource
     */
    public function createAvatar(UploadedFile $file): mixed
    {
        return $this->manager
            ->decodePath($file->getPathname())
            ->cover(self::AVATAR_SIZE, self::AVATAR_SIZE)
            ->encodeUsingFormat(Format::WEBP, self::AVATAR_QUALITY)
            ->toStream()
        ;
    }
}
