<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class Slug implements \Stringable
{
    private const string REGEX = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function tryFrom(SluggerInterface $slugger, string $value): self
    {
        $slugValue = $slugger
            ->slug($value)
            ->toString()
         |> strtolower(...)
        ;

        return new self($slugValue);
    }

    private function validate(): void
    {
        if ('' === $this->value) {
            return;
        }

        if (!preg_match(self::REGEX, $this->value, $matches)) {
            throw new \ValueError('Invalid slug with value: '.$this->value);
        }
    }
}
