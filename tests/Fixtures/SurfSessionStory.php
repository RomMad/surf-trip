<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\Title;
use App\Enum\SurfSession\SurfSessionRating;
use App\Factory\SurfSessionFactory;
use App\Factory\TripFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class SurfSessionStory extends Story
{
    public const string SPOT = 'Hossegor';
    public const string BOARD = 'JS Monsta 5.8';
    public const string OBJECTIVE = 'Improve my carving technique';
    public const string COMMENT = 'Great session with amazing waves';

    public function build(): void
    {
        $user = UserFactory::find(['email' => Email::from(UserStory::JOHN_EMAIL)]);
        $trip = TripFactory::find(['title' => Title::from(TripStory::CURRENT_TRIP_TITLE)]);

        SurfSessionFactory::createMany(15, [
            'user' => $user,
        ]);

        SurfSessionFactory::createOne([
            'spot' => self::SPOT,
            'board' => self::BOARD,
            'startAt' => new \DateTimeImmutable('-1 day 10:00'),
            'endAt' => new \DateTimeImmutable('-1 day 12:00'),
            'rating' => SurfSessionRating::Good,
            'objective' => self::OBJECTIVE,
            'comment' => self::COMMENT,
            'user' => $user,
            'trip' => $trip,
        ]);
    }
}
