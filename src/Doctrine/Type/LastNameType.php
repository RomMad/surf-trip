<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\ValueObject\LastName;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class LastNameType extends StringType
{
    public const NAME = 'last_name';
    public const LENGTH = 100;

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => self::LENGTH] + $column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $lastName, AbstractPlatform $platform): ?string
    {
        return $lastName instanceof LastName ? $lastName->getValue() : null;
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?LastName
    {
        return null !== $value && '' !== $value ? new LastName($value) : null;
    }
}
