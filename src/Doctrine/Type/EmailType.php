<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\ValueObject\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EmailType extends StringType
{
    public const NAME = 'email';
    public const LENGTH = 180;

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => self::LENGTH] + $column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $email, AbstractPlatform $platform): ?string
    {
        return $email instanceof Email ? $email->getValue() : null;
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Email
    {
        return null !== $value ? new Email($value) : null;
    }
}
