<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class TranslatableEnumNormalizer implements NormalizerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {}

    /**
     * @param TranslatableInterface&\UnitEnum $data
     *
     * @return array{value: int|string, label: string}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $locale = $context['locale'] ?? null;

        return [
            'value' => $data instanceof \BackedEnum ? $data->value : $data->name,
            'label' => $data->trans($this->translator, $locale),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof \UnitEnum && $data instanceof TranslatableInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            TranslatableInterface::class => false,
        ];
    }
}
