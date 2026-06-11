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
        public ?string $placeId = null
    ) {
        Assert::minLength($label, self::MIN_LENGTH, 'location.min_length');
        Assert::maxLength($label, self::MAX_LENGTH, 'location.max_length');

        if (in_array(null, [$latitude, $longitude, $placeId], true)) {
            return;
        }

        Assert::range($latitude, -90, 90);
        Assert::range($longitude, -180, 180);

        Assert::maxLength($placeId, self::MAX_LENGTH, 'location.place_id_max_length');
    }
}
