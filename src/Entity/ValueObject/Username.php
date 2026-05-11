<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Username extends AbstractValueObject
{
    use ValueObjectTrait;

    private const int MIN_LENGTH = 3;
    private const int MAX_LENGTH = 50;

    public function __construct(public string $value)
    {
        Assert::notEmpty($value, 'user.username.not_blank');
        Assert::minLength($value, self::MIN_LENGTH, 'user.username.min_length');
        Assert::maxLength($value, self::MAX_LENGTH, 'user.username.max_length');
        Assert::regex($value, '/^[a-zA-Z0-9._]+$/', 'user.username.invalid_format');
    }
}
