<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Webmozart\Assert\Assert;

final readonly class FirstName extends AbstractValueObject
{
    use ValueObjectTrait;

    private const int MAX_LENGTH = 100;

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        Assert::notEmpty($value, 'user.first_name.not_blank');
        Assert::maxLength($value, self::MAX_LENGTH, 'user.first_name.max_length');

        $this->value = ucfirst($value);
    }
}
