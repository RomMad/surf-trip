<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Location extends AbstractValueObject
{
    use ValueObjectTrait;

    private const int MIN_LENGTH = 3;
    private const int MAX_LENGTH = 255;

    public function __construct(public string $value)
    {
        Assert::notEmpty($value, 'trip.location.not_blank');
        Assert::minLength($value, self::MIN_LENGTH, 'trip.location.min_length');
        Assert::maxLength($value, self::MAX_LENGTH, 'trip.location.max_length');
    }
}
