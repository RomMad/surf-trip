<?php

declare(strict_types=1);

namespace App\Twig\Function;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Attribute\AsTwigFunction;

final readonly class AvatarUrl
{
    public function __construct(
        private Packages $packages,
        #[Autowire('%app.avatar.path%')]
        private string $avatarPath,
    ) {}

    #[AsTwigFunction('avatar_url')]
    public function generateAvatarUrl(string $filename): string
    {
        return $this->packages->getUrl(sprintf('%s/%s', $this->avatarPath, $filename));
    }
}
