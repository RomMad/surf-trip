<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\ValueObject\FirstName;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class FirstNameType extends StringType
{
    public const NAME = 'first_name';
    public const LENGTH = 100;

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => self::LENGTH] + $column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $firstName, AbstractPlatform $platform): ?string
    {
        return $firstName instanceof FirstName ? $firstName->getValue() : null;
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?FirstName
    {
        return null !== $value ? new FirstName($value) : null;
    }
}
