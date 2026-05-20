<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SurfSession;
use App\Entity\User;
use App\Enum\SurfSession\SurfSessionRating;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class SurfSessionFixtures extends Fixture implements DependentFixtureInterface
{
    private const int RANDOM_SESSIONS_COUNT = 400;
    private const int USER_REFERENCE_COUNT = 6;

    private const array SPOTS = [
        'Seignosse Les Bourdaines',
        'Hossegor La Gravière',
        'Lacanau Sud',
        'La Torche, Bretagne',
        'Quiberon Port Blanc',
        'Peniche, Portugal',
        'Ericeira, Portugal',
        'Taghazout, Morocco',
        'Darkhla, Morocco',
        'Gerupuk, Lombok',
        'Canggu, Bali',
        'Mundaka, Spain',
        'Fuerteventura, Spain',
    ];

    private const array BOARDS = [
        "Shortboard 5'10",
        "Shortboard 6'0",
        "Fish 5'8",
        "Funboard 7'2",
        "Longboard 9'0",
        "Mid-length 7'0",
        "Mini Malibu 7'6",
    ];

    private const array OBJECTIVES = [
        'Work on take-off timing',
        'Improve bottom turn consistency',
        'Practice reading sets and positioning',
        'Focus on carving and rail transitions',
        'Build endurance for longer sessions',
        'Train duck dive efficiency',
    ];

    private const array COMMENTS = [
        'Clean waves early, light offshore wind, good energy in the water.',
        'Crowded peak but managed to catch quality waves on the shoulder.',
        'Tide dropped quickly, had to adjust positioning every 15 minutes.',
        'Strong paddling session, felt more stable on late take-offs.',
        'Small but fun conditions, perfect for technique-focused practice.',
        'Powerful sets and strong current, physically demanding but rewarding.',
    ];

    private const array SESSIONS_DATA = [
        [
            'spot' => 'Hossegor, France',
            'board' => "Shortboard 6'0",
            'startAt' => '-10 days 07:00',
            'durationMinutes' => 110,
            'rating' => SurfSessionRating::Good,
            'objective' => 'Practice backside turns in steeper sections',
            'comment' => 'Solid chest-high waves with clean faces at first light.',
        ],
        [
            'spot' => 'Peniche, Portugal',
            'board' => "Fish 5'8",
            'startAt' => '-8 days 17:30',
            'durationMinutes' => 95,
            'rating' => SurfSessionRating::Average,
            'objective' => 'Work on generating speed in weaker sections',
            'comment' => 'Soft evening swell, fun walls but inconsistent sets.',
        ],
        [
            'spot' => 'Taghazout, Morocco',
            'board' => "Longboard 9'0",
            'startAt' => '-6 days 08:15',
            'durationMinutes' => 140,
            'rating' => SurfSessionRating::Excellent,
            'objective' => 'Trim and nose-riding on cleaner right-handers',
            'comment' => 'Long peeling rights and almost no crowd at sunrise.',
        ],
        [
            'spot' => 'Canggu, Bali',
            'board' => "Shortboard 5'10",
            'startAt' => '-4 days 06:45',
            'durationMinutes' => 80,
            'rating' => SurfSessionRating::Bad,
            'objective' => 'Stay consistent on late drops',
            'comment' => 'Wind picked up early and made sections bumpy and difficult.',
        ],
        [
            'user' => 4,
            'spot' => 'Biarritz, France',
            'board' => "Mid-length 7'0",
            'startAt' => '-2 days 18:00',
            'durationMinutes' => 70,
            'rating' => SurfSessionRating::VeryBad,
            'objective' => 'Keep paddling rhythm and improve wave selection',
            'comment' => 'Very small and mushy surf, little power to work with.',
        ],
        [
            'spot' => 'Fuerteventura, Spain',
            'board' => null,
            'startAt' => '-1 day 07:20',
            'durationMinutes' => 120,
            'rating' => SurfSessionRating::Good,
            'objective' => null,
            'comment' => 'Good mix of push and shape, nice progression through the session.',
        ],
    ];

    private readonly Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->generateSurfSessions() as $surfSession) {
            $manager->persist($surfSession);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    /**
     * @return \Generator<SurfSession>
     */
    private function generateSurfSessions(): \Generator
    {
        for ($index = 0; $index < self::RANDOM_SESSIONS_COUNT; ++$index) {
            /** @var \DateTimeInterface $randomStart */
            $randomStart = $this->faker->dateTimeBetween('-6 months', 'now');
            $startAt = \DateTimeImmutable::createFromInterface($randomStart);
            $durationMinutes = $this->faker->numberBetween(45, 180);

            $surfSession = new SurfSession();
            $surfSession->board = $this->faker->optional(0.8)->randomElement(self::BOARDS);
            $surfSession->spot = $this->faker->randomElement(self::SPOTS);
            $surfSession->startAt = $startAt;
            $surfSession->endAt = $startAt->modify(sprintf('+%d minutes', $durationMinutes));
            $surfSession->rating = $this->faker->optional(0.9)->randomElement(SurfSessionRating::cases());
            $surfSession->objective = $this->faker->optional(0.7)->randomElement(self::OBJECTIVES);
            $surfSession->comment = $this->faker->optional(0.8)->randomElement(self::COMMENTS);
            $surfSession->user = $this->getRandomUser();

            yield $surfSession;
        }

        foreach (self::SESSIONS_DATA as $index => $sessionData) {
            $startAt = new \DateTimeImmutable($sessionData['startAt']);

            $surfSession = new SurfSession();
            $surfSession->board = $sessionData['board'];
            $surfSession->spot = $sessionData['spot'];
            $surfSession->startAt = $startAt;
            $surfSession->endAt = $startAt->modify(sprintf('+%d minutes', $sessionData['durationMinutes']));
            $surfSession->rating = $sessionData['rating'];
            $surfSession->objective = $sessionData['objective'];
            $surfSession->comment = $sessionData['comment'];
            /** @var User $user */
            $user = $this->getReference(UserFixtures::USER_REFERENCE.$index, User::class);
            $surfSession->user = $user;

            yield $surfSession;
        }
    }

    private function getRandomUser(): User
    {
        $randomUserIndex = $this->faker->numberBetween(0, self::USER_REFERENCE_COUNT - 1);

        return $this->getReference(UserFixtures::USER_REFERENCE.$randomUserIndex, User::class);
    }
}
