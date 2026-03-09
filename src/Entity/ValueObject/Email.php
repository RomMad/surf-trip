<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Email extends AbstractValueObject
{
    use ValueObjectTrait;

    private const int MAX_LENGTH = 180;

    public function __construct(public string $value)
    {
        Assert::notEmpty($value, 'user.email.not_blank');
        Assert::email($value, 'user.email.invalid');
        Assert::maxLength($value, self::MAX_LENGTH, 'user.email.max_length');
    }
}
