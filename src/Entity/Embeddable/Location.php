<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Location
{
    private const int MIN_LENGTH = 3;
    private const int MAX_LENGTH = 255;

    public function __construct(
        #[ORM\Column(nullable: false)]
        public string $label,
        #[ORM\Column(nullable: true)]
        public ?float $latitude = null,
        #[ORM\Column(nullable: true)]
        public ?float $longitude = null,
        #[ORM\Column(nullable: true)]
        public ?string $placeId = null,
        #[ORM\Column(nullable: true)]
        public ?string $comment = null,
    ) {
        Assert::minLength($label, self::MIN_LENGTH, 'location.label.min_length');
        Assert::maxLength($label, self::MAX_LENGTH, 'location.label.max_length');

        if (in_array(null, [$latitude, $longitude, $placeId], true)) {
            return;
        }

        Assert::range($latitude, -90, 90);
        Assert::range($longitude, -180, 180);

        Assert::maxLength($placeId, self::MAX_LENGTH);

        if (null !== $comment) {
            Assert::maxLength($comment, self::MAX_LENGTH, 'location.comment.max_length');
        }
    }
}
