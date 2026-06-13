<?php

declare(strict_types=1);

namespace App\Form\Model\Shared;

use Symfony\Component\Validator\Constraints as Assert;

final class LocationInput
{
    #[Assert\NotBlank(message: 'location.label.not_blank')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'location.label.min_length', maxMessage: 'location.label.max_length')]
    public string $label = '';

    #[Assert\Range(min: -90, max: 90)]
    public ?float $latitude = null;

    #[Assert\Range(min: -180, max: 180)]
    public ?float $longitude = null;

    #[Assert\Length(max: 255)]
    public ?string $placeId = null;

    #[Assert\Length(max: 255, maxMessage: 'location.comment.max_length')]
    public ?string $comment = null;
}
