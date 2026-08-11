<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Webmozart\Assert\Assert;

trait LocationValidationTrait
{
    private const int MIN_LENGTH = 3;
    private const int MAX_LENGTH = 255;

    private function validateLabel(string $label): void
    {
        Assert::minLength($label, self::MIN_LENGTH, 'location.label.min_length');
        Assert::maxLength($label, self::MAX_LENGTH, 'location.label.max_length');
    }

    private function validateCoordinates(?float $latitude, ?float $longitude, ?string $placeId): void
    {
        if (in_array(null, [$latitude, $longitude, $placeId], true)) {
            return;
        }

        Assert::range($latitude, -90, 90);
        Assert::range($longitude, -180, 180);
        Assert::maxLength($placeId, self::MAX_LENGTH);
    }

    private function validateComment(?string $comment): void
    {
        if (null !== $comment) {
            Assert::maxLength($comment, self::MAX_LENGTH, 'location.comment.max_length');
        }
    }
}
