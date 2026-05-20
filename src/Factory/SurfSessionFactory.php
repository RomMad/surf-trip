<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\SurfSession;
use App\Enum\SurfSession\SurfSessionRating;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<SurfSession>
 */
final class SurfSessionFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return SurfSession::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        /** @var \DateTimeInterface $randomStart */
        $randomStart = self::faker()->dateTimeBetween('-2 months', '-2 days');
        $startAt = \DateTimeImmutable::createFromInterface($randomStart);

        return [
            'board' => self::faker()->optional()->word(),
            'spot' => self::faker()->city(),
            'startAt' => $startAt,
            'endAt' => $startAt->modify(sprintf('+%d hours', self::faker()->numberBetween(1, 4))),
            'rating' => self::faker()->optional()->randomElement(SurfSessionRating::cases()),
            'objective' => self::faker()->optional()->sentence(),
            'comment' => self::faker()->optional()->paragraph(),
            'user' => UserFactory::randomOrCreate(),
        ];
    }
}
