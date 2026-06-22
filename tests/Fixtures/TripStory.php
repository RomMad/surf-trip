<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\Embeddable\Location;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\Title;
use App\Enum\User\SurfLevel;
use App\Factory\TripFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class TripStory extends Story
{
    public const string TRIP_TITLE = 'Future trip';
    public const string TRIP_LOCATION = 'Bali, Indonesia';
    public const string TRIP_DESCRIPTION = 'A trip to Bali with friends.';
    public const array TRIP_CRITERIA = [
        'title' => self::TRIP_TITLE,
    ];

    public const string PAST_TRIP_TITLE = 'Past trip';
    public const string CURRENT_TRIP_TITLE = 'Current trip';

    public function build(): void
    {
        TripFactory::createMany(18, [
            'owners' => [UserFactory::random()],
        ]);

        $owner = UserFactory::find(['email' => Email::from(UserStory::JOHN_EMAIL)]);

        TripFactory::createOne([
            'title' => Title::from(self::PAST_TRIP_TITLE),
            'startAt' => new \DateTimeImmutable('-4 weeks'),
            'endAt' => new \DateTimeImmutable('-3 weeks'),
            'owners' => [$owner],
        ]);

        TripFactory::createOne([
            'title' => Title::from(self::CURRENT_TRIP_TITLE),
            'startAt' => new \DateTimeImmutable('-2 days'),
            'endAt' => new \DateTimeImmutable('+5 days'),
            'owners' => [$owner],
        ]);

        TripFactory::createOne([
            'title' => Title::from(self::TRIP_TITLE),
            'location' => new Location(self::TRIP_LOCATION, -8.4095, 115.1889),
            'startAt' => new \DateTimeImmutable('+1 month'),
            'endAt' => new \DateTimeImmutable('+1 month +10 days'),
            'description' => self::TRIP_DESCRIPTION,
            'requiredLevels' => [SurfLevel::Beginner, SurfLevel::Intermediate],
            'owners' => [$owner],
        ]);
    }
}
