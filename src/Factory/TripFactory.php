<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Embeddable\Location;
use App\Entity\Trip;
use App\Entity\ValueObject\Title;
use App\Enum\User\SurfLevel;
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
        $words = self::faker()->words(random_int(2, 4), true);
        $title = sprintf('%s Surf Trip', ucfirst(is_string($words) ? $words : ''));
        $location = sprintf('%s, %s', self::faker()->city(), self::faker()->country());
        $creator = UserFactory::randomOrCreate();

        return [
            'title' => Title::from($title),
            'location' => new Location(
                $location,
                self::faker()->latitude(),
                self::faker()->longitude()
            ),
            'startAt' => $startAt,
            'endAt' => $startAt->modify(sprintf('+%d days', self::faker()->numberBetween(3, 14))),
            'requiredLevels' => self::randomSurfLevels(),
            'description' => self::faker()->paragraphs(self::faker()->numberBetween(1, 3), true),
            'owners' => [$creator],
            'createdBy' => $creator,
            'createdAt' => $createdAt,
        ];
    }

    /**
     * @return list<SurfLevel>
     */
    public static function randomSurfLevels(): array
    {
        $levels = SurfLevel::cases();
        $count = self::faker()->numberBetween(1, 3);
        $requiredLevels = self::faker()->randomElements($levels, $count, true);
        $requiredLevels = array_unique($requiredLevels, SORT_REGULAR);

        usort($requiredLevels, fn (SurfLevel $a, SurfLevel $b) => $a->value <=> $b->value);

        return $requiredLevels;
    }
}
