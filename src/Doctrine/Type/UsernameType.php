<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\ValueObject\Username;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class UsernameType extends StringType
{
    public const NAME = 'username';
    public const LENGTH = 50;

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => self::LENGTH] + $column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $username, AbstractPlatform $platform): ?string
    {
        return $username instanceof Username ? $username->getValue() : null;
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Username
    {
        return null !== $value ? new Username($value) : null;
    }
}
