<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\ValueObject\Title;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TitleType extends StringType
{
    public const NAME = 'title';
    public const LENGTH = 255;

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => self::LENGTH] + $column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $title, AbstractPlatform $platform): ?string
    {
        return $title instanceof Title ? $title->getValue() : null;
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Title
    {
        return $value ? new Title($value) : null;
    }
}
