<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\ValueObject\Location;
use App\Entity\ValueObject\Title;
use App\Enum\Trip\RequiredLevel;
use App\Factory\TripFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class TripStory extends Story
{
    public const string TRIP_TITLE = 'Trip to Bali';
    public const string TRIP_LOCATION = 'Bali, Indonesia';
    public const string TRIP_DESCRIPTION = 'A trip to Bali with friends.';
    public const array TRIP_CRITERIA = [
        'title' => self::TRIP_TITLE,
    ];

    public function build(): void
    {
        TripFactory::createMany(20, [
            'owners' => [UserFactory::random()],
        ]);

        TripFactory::createOne([
            'title' => Title::from(self::TRIP_TITLE),
            'location' => Location::from(self::TRIP_LOCATION),
            'startAt' => new \DateTimeImmutable('+1 month'),
            'endAt' => new \DateTimeImmutable('+1 month +10 days'),
            'description' => self::TRIP_DESCRIPTION,
            'requiredLevels' => [RequiredLevel::Beginner, RequiredLevel::Intermediate],
            'owners' => [UserFactory::find(['email' => UserStory::JOHN_EMAIL])],
        ]);
    }
}
