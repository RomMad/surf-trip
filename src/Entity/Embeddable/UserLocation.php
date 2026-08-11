<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class UserLocation
{
    use LocationValidationTrait;

    public function __construct(
        #[ORM\Column(nullable: true)]
        public ?string $label = null,
        #[ORM\Column(nullable: true)]
        public ?float $latitude = null,
        #[ORM\Column(nullable: true)]
        public ?float $longitude = null,
        #[ORM\Column(nullable: true)]
        public ?string $placeId = null,
    ) {
        if (null === $label || '' === $label) {
            return;
        }

        $this->validateLabel($label);
        $this->validateCoordinates($latitude, $longitude, $placeId);
    }
}
