<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Trip;
use App\Entity\ValueObject\Location;
use App\Entity\ValueObject\Title;
use App\Enum\Trip\RequiredLevel;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Trip>
 */
final class TripFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Trip::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        /** @var \DateTimeInterface $randomStart */
        $randomStart = self::faker()->dateTimeBetween('-6 months', '+6 months');
        $startAt = \DateTimeImmutable::createFromInterface($randomStart);
        $createdAt = \DateTimeImmutable::createFromInterface(self::faker()->dateTimeBetween('-1 year', '-1 month'));
        $title = sprintf('%s Surf Trip', ucfirst((string) self::faker()->words(random_int(2, 4), true)));
        $location = sprintf('%s, %s', self::faker()->city(), self::faker()->country());
        $requiredLevels = self::faker()->randomElements(
            RequiredLevel::cases(),
            self::faker()->numberBetween(1, count(RequiredLevel::cases()))
        );

        return [
            'title' => Title::from($title),
            'location' => Location::from($location),
            'startAt' => $startAt,
            'endAt' => $startAt->modify(sprintf('+%d days', self::faker()->numberBetween(3, 14))),
            'requiredLevels' => $requiredLevels,
            'description' => self::faker()->paragraphs(self::faker()->numberBetween(1, 3), true),
            'createdAt' => $createdAt,
        ];
    }
}
