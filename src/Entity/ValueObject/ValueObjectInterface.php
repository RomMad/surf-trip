<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

interface ValueObjectInterface extends \Stringable
{
    public function __toString(): string;

    public function getValue(): string;
}
