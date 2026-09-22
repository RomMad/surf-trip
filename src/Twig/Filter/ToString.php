<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Attribute\AsTwigFilter;

final readonly class ToString
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {}

    #[AsTwigFilter('to_string')]
    public function toString(mixed $value): string
    {
        return match (true) {
            $value instanceof FieldInterface => $this->convertFieldToString($value),
            $value instanceof TranslatableInterface => $value->trans($this->translator),
            $value instanceof \DateTimeInterface => $value->format('d/m/Y'),
            $value instanceof \Stringable => $value->__toString(),
            $value instanceof Collection => implode(', ', array_map(fn ($v) => $this->toString($v), $value->toArray())),
            is_array($value) => implode(', ', array_map(fn ($v) => $this->toString($v), $value)),
            default => (string) $value,
        };
    }

    private function convertFieldToString(FieldInterface $field): string
    {
        $value = $field->getAsDto()->getValue();

        return match (true) {
            $field instanceof DateTimeField,
            $field instanceof DateField => $value instanceof \DateTimeInterface ? $value->format($field->getAsDto()->getFormattedValue()) : (string) $value,
            default => (string) $field->getAsDto()->getFormattedValue(),
        };
    }
}
