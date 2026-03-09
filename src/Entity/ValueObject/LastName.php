<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Webmozart\Assert\Assert;

final readonly class LastName extends AbstractValueObject
{
    use ValueObjectTrait;

    private const int MAX_LENGTH = 100;

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        Assert::maxLength($value, self::MAX_LENGTH, 'user.last_name.max_length');

        $this->value = strtoupper($value);
    }
}
