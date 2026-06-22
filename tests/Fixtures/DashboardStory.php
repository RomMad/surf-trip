<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\Embeddable\Location;
use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\FirstName;
use App\Entity\ValueObject\LastName;
use App\Entity\ValueObject\Title;
use App\Entity\ValueObject\Username;
use App\Enum\SurfSession\SurfSessionRating;
use App\Enum\User\Locale;
use App\Factory\SurfSessionFactory;
use App\Factory\TripFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class DashboardStory extends Story
{
    public const string USER_EMAIL = 'john.dashboard@test.com';
    public const string USER_USERNAME = 'john.dashboard';
    public const string OTHER_USER_EMAIL = 'jane.dashboard@test.com';
    public const string OTHER_USER_USERNAME = 'jane.dashboard';
    public const string USER_WITHOUT_ACTIVITY_EMAIL = 'empty.dashboard@test.com';

    public function build(): void
    {
        $user = UserFactory::createOne([
            'email' => Email::from(self::USER_EMAIL),
            'username' => Username::from(self::USER_USERNAME),
            'firstName' => FirstName::from('John'),
            'lastName' => LastName::from('Doe'),
            'locale' => Locale::English,
        ]);

        $otherUser = UserFactory::createOne([
            'email' => Email::from(self::OTHER_USER_EMAIL),
            'username' => Username::from(self::OTHER_USER_USERNAME),
            'firstName' => FirstName::from('Jane'),
            'lastName' => LastName::from('Doe'),
            'locale' => Locale::French,
        ]);

        $userWithoutActivity = UserFactory::createOne([
            'email' => Email::from('empty.dashboard@test.com'),
            'username' => Username::from('empty.dashboard'),
        ]);

        $currentYear = (int) new \DateTimeImmutable()->format('Y');
        $previousYear = $currentYear - 1;

        TripFactory::createOne([
            'title' => Title::from('Current year trip'),
            'location' => new Location('Hossegor, France', 43.6636, -1.4419),
            'startAt' => new \DateTimeImmutable(sprintf('%d-03-10 08:00:00', $currentYear)),
            'endAt' => new \DateTimeImmutable(sprintf('%d-03-17 18:00:00', $currentYear)),
            'owners' => [$user],
        ]);

        TripFactory::createOne([
            'title' => Title::from('Previous year trip'),
            'location' => new Location('Biarritz, France', 43.4832, -1.5586),
            'startAt' => new \DateTimeImmutable(sprintf('%d-05-10 08:00:00', $previousYear)),
            'endAt' => new \DateTimeImmutable(sprintf('%d-05-17 18:00:00', $previousYear)),
            'owners' => [$user],
        ]);

        TripFactory::createOne([
            'title' => Title::from('Other user trip'),
            'location' => new Location('Taghazout, Morocco', 30.4278, -9.6542),
            'startAt' => new \DateTimeImmutable(sprintf('%d-06-10 08:00:00', $currentYear)),
            'endAt' => new \DateTimeImmutable(sprintf('%d-06-17 18:00:00', $currentYear)),
            'owners' => [$otherUser],
        ]);

        SurfSessionFactory::createOne([
            'spot' => 'Hossegor',
            'board' => 'JS Monsta',
            'startAt' => new \DateTimeImmutable(sprintf('%d-01-10 09:00:00', $currentYear)),
            'endAt' => new \DateTimeImmutable(sprintf('%d-01-10 11:00:00', $currentYear)),
            'rating' => SurfSessionRating::Good,
            'user' => $user,
        ]);

        SurfSessionFactory::createOne([
            'spot' => 'Hossegor',
            'board' => 'Pyzel',
            'startAt' => new \DateTimeImmutable(sprintf('%d-02-10 09:00:00', $currentYear)),
            'endAt' => new \DateTimeImmutable(sprintf('%d-02-10 11:00:00', $currentYear)),
            'rating' => null,
            'user' => $user,
        ]);

        SurfSessionFactory::createOne([
            'spot' => 'La Torche',
            'board' => 'Firewire',
            'startAt' => new \DateTimeImmutable(sprintf('%d-03-10 09:00:00', $currentYear)),
            'endAt' => new \DateTimeImmutable(sprintf('%d-03-10 11:00:00', $currentYear)),
            'rating' => SurfSessionRating::Bad,
            'user' => $user,
        ]);

        SurfSessionFactory::createOne([
            'spot' => 'Biarritz',
            'board' => 'Lost',
            'startAt' => new \DateTimeImmutable(sprintf('%d-04-10 09:00:00', $previousYear)),
            'endAt' => new \DateTimeImmutable(sprintf('%d-04-10 11:00:00', $previousYear)),
            'rating' => SurfSessionRating::Excellent,
            'user' => $user,
        ]);
    }

    public static function getDashboardUser(): User
    {
        return UserFactory::find(['email' => Email::from(self::USER_EMAIL)]);
    }
}
