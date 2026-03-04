<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

final readonly class Slug extends AbstractValueObject
{
    use ValueObjectTrait;

    private const string REGEX = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public function __construct(public string $value)
    {
        if ('' === $value) {
            return;
        }

        if (!preg_match(self::REGEX, $value, $matches)) {
            throw new \ValueError('Invalid slug with value: '.$value);
        }
    }
}
