<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

trait ValueObjectTrait
{
    public static function from(string $value): static
    {
        return new static($value);
    }

    public static function tryFrom(?string $value): ?static
    {
        return $value ? new static($value) : null;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
