<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\ValueObject\Location;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class LocationType extends StringType
{
    public const NAME = 'location';
    public const LENGTH = 255;

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => self::LENGTH] + $column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $location, AbstractPlatform $platform): ?string
    {
        return $location instanceof Location ? $location->getValue() : null;
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Location
    {
        return null !== $value ? new Location($value) : null;
    }
}
