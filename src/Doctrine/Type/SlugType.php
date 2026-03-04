<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\ValueObject\Slug;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class SlugType extends StringType
{
    public const string NAME = 'slug';
    public const int LENGTH = 255;

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => self::LENGTH] + $column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $slug, AbstractPlatform $platform): ?string
    {
        return $slug instanceof Slug ? $slug->value : null;
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Slug
    {
        return Slug::tryFrom($value);
    }
}
