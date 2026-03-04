<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

abstract readonly class AbstractValueObject implements ValueObjectInterface
{
    public function __toString(): string
    {
        return (string) $this->getValue();
    }

    public function equals(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }
}
