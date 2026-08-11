<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Location
{
    use LocationValidationTrait;

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
        $this->validateLabel($label);
        $this->validateCoordinates($latitude, $longitude, $placeId);
        $this->validateComment($comment);
    }

    public function getFullLabel(): string
    {
        if (null === $this->comment) {
            return $this->label;
        }

        return sprintf('%s (%s)', $this->label, $this->comment);
    }
}
