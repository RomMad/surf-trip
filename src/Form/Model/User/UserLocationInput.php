<?php

declare(strict_types=1);

namespace App\Form\Model\User;

use Symfony\Component\Validator\Constraints as Assert;

final class UserLocationInput
{
    #[Assert\Regex(pattern: '/^$|^.{3,}$/u', message: 'location.label.min_length')]
    #[Assert\Length(max: 255, maxMessage: 'location.label.max_length')]
    public string $label = '';

    #[Assert\Range(min: -90, max: 90)]
    public ?float $latitude = null;

    #[Assert\Range(min: -180, max: 180)]
    public ?float $longitude = null;

    #[Assert\Length(max: 255)]
    public ?string $placeId = null;
}
