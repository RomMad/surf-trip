<?php

declare(strict_types=1);

namespace App\Controller\Admin;

trait AvatarUrlTrait
{
    private string $avatarPath;

    private function generateAvatarUrl(?string $filename = null): ?string
    {
        if (!$filename) {
            return null;
        }

        return sprintf('/%s/%s', $this->avatarPath, $filename);
    }
}
