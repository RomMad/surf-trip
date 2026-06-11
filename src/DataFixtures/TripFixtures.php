<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Data\SurfTripData;
use App\Entity\Embeddable\Location;
use App\Entity\Trip;
use App\Entity\User;
use App\Entity\ValueObject\Title;
use App\Enum\User\SurfLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class TripFixtures extends Fixture implements DependentFixtureInterface
{
    private const int TRIPS_COUNT = 1500;

    private const array TRIP_TITLE_PREFIXES = [
        'Dawn Patrol',
        'Wave Hunter',
        'Barrel Quest',
        'Coastal Escape',
        'Lineup Legends',
        'Swell Chasers',
        'Saltwater Journey',
        'Blue Horizon',
        'Reef & Point Adventure',
        'Tide Riders',
    ];

    private const array TRIP_TITLE_SUFFIXES = [
        'Surf Camp',
        'Surf Expedition',
        'Surf Retreat',
        'Surf Adventure',
        'Surf Sessions',
        'Surf Experience',
        'Wave Mission',
        'Wave Odyssey',
    ];

    private const array TRIPS_DATA = [
        [
            'title' => 'Summer Surf Adventure Bali',
            'location' => 'Bali, Indonesia',
            'startAt' => '2025-06-01',
            'endAt' => '2025-06-15',
            'requiredLevels' => [SurfLevel::Beginner, SurfLevel::Intermediate],
            'description' => <<<'DESC'
    Experience the best waves of Bali with experienced instructors.
    Perfect for beginners looking to improve their skills.
    DESC,
            'owners' => [0, 1],
        ],
        [
            'title' => 'Advanced Surfing in Portugal',
            'location' => 'Nazaré, Portugal',
            'startAt' => '2025-07-10',
            'endAt' => '2025-07-20',
            'requiredLevels' => [SurfLevel::Advanced],
            'description' => <<<'DESC'
Challenge yourself with some of the biggest waves in the world.
Only for experienced surfers.
DESC,
            'owners' => [1],
        ],
        [
            'title' => 'Tropical Waves Hawaii',
            'location' => 'Honolulu, Hawaii',
            'startAt' => '2025-08-01',
            'endAt' => '2025-08-10',
            'requiredLevels' => [SurfLevel::Intermediate],
            'description' => <<<'DESC'
Ride the iconic waves of Hawaii with breathtaking views.
Great for intermediate surfers wanting to explore new breaks.
DESC,
            'owners' => [2, 3],
        ],
        [
            'title' => 'Costa Rica Epic Journey',
            'location' => 'Guanacaste, Costa Rica',
            'startAt' => '2025-09-05',
            'endAt' => '2025-09-18',
            'requiredLevels' => [SurfLevel::Beginner, SurfLevel::Intermediate, SurfLevel::Advanced],
            'description' => <<<'DESC'
A comprehensive surf trip with waves for all levels.
Explore multiple breaks and tropical landscapes.
DESC,
            'owners' => [0, 2],
        ],
        [
            'title' => 'Beginner Paradise Fuerteventura',
            'location' => 'Fuerteventura, Spain',
            'startAt' => '2025-05-15',
            'endAt' => '2025-05-25',
            'requiredLevels' => [SurfLevel::Beginner],
            'description' => <<<'DESC'
Perfect destination for learning to surf.
Calm, consistent waves and beautiful beaches.
DESC,
            'owners' => [3, 4],
        ],
        [
            'title' => 'Winter Swell of Tahiti',
            'location' => "Teahupo'o, Tahiti",
            'startAt' => '2025-11-01',
            'endAt' => '2025-11-12',
            'requiredLevels' => [SurfLevel::Advanced],
            'description' => <<<'DESC'
Experience the legendary barrel at Teahupo'o.
For expert surfers only.
DESC,
            'owners' => [1, 4],
        ],
        [
            'title' => 'Australian East Coast Explorer',
            'location' => 'Byron Bay, Australia',
            'startAt' => '2025-10-20',
            'endAt' => '2025-10-30',
            'requiredLevels' => [SurfLevel::Beginner, SurfLevel::Intermediate],
            'description' => <<<'DESC'
Discover the vibrant surf culture of East Australia.
Multiple breaks suitable for various skill levels.
DESC,
            'owners' => [0],
        ],
        [
            'title' => 'Summer Sessions Morocco',
            'location' => 'Essaouira, Morocco',
            'startAt' => '2025-07-01',
            'endAt' => '2025-07-08',
            'requiredLevels' => [SurfLevel::Intermediate],
            'description' => 'Enjoy the summer swells of Morocco with a perfect blend of culture and surfing.',
            'owners' => [3],
        ],
    ];

    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        foreach ($this->generateTrips() as $trip) {
            $manager->persist($trip);
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
     * @return \Generator<Trip>
     */
    private function generateTrips(): \Generator
    {
        // Generate random trips
        for ($index = 0; $index < self::TRIPS_COUNT; ++$index) {
            /** @var \DateTimeInterface $randomStart */
            $randomStart = $this->faker->dateTimeBetween('-5 years', '+6 months');
            $startAt = \DateTimeImmutable::createFromInterface($randomStart);
            $createdAt = \DateTimeImmutable::createFromInterface($this->faker->dateTimeBetween('-1 year', '-1 month'));
            $tripData = $this->faker->randomElement(SurfTripData::ALL);
            $location = $tripData['location'];
            $title = $this->randomTripTitle($location['label']);

            $trip = new Trip($createdAt);
            $trip->title = Title::from($title);
            $trip->location = new Location(
                sprintf('%s, %s', $location['label'], $tripData['country']),
                $location['latitude'],
                $location['longitude'],
                $location['placeId'],
            );
            $trip->startAt = $startAt;
            $trip->endAt = $startAt->modify(sprintf('+%d days', $this->faker->numberBetween(3, 14)));
            $trip->requiredLevels = $this->randomSurfLevels();
            $trip->description = $this->faker->paragraphs($this->faker->numberBetween(1, 3), true);

            /** @var list<int> $owners */
            $owners = $this->faker->randomElements(range(0, UserFixtures::USERS_COUNT - 1), $this->faker->numberBetween(1, 3));

            foreach ($owners as $ownerIndex) {
                /** @var User $owner */
                $owner = $this->getReference(UserFixtures::USER_REFERENCE.$ownerIndex, User::class);
                $trip->addOwner($owner);
            }

            yield $trip;
        }

        // Generate predefined trips
        foreach (self::TRIPS_DATA as $tripData) {
            $createdAt = \DateTimeImmutable::createFromInterface($this->faker->dateTimeBetween('-1 month', 'today'));

            $trip = new Trip($createdAt);
            $trip->title = Title::from($tripData['title']);
            $location = $this->getLocationData($tripData['location']);
            $trip->location = new Location(
                $tripData['location'],
                $location['latitude'],
                $location['longitude'],
                $location['placeId'],
            );
            $trip->startAt = new \DateTimeImmutable($tripData['startAt']);
            $trip->endAt = new \DateTimeImmutable($tripData['endAt']);
            $trip->requiredLevels = $tripData['requiredLevels'];
            $trip->description = $tripData['description'];

            foreach ($tripData['owners'] as $ownerIndex) {
                /** @var User $owner */
                $owner = $this->getReference(UserFixtures::USER_REFERENCE.$ownerIndex, User::class);
                $trip->addOwner($owner);
            }

            yield $trip;
        }
    }

    /**
     * @return list<SurfLevel>
     */
    private function randomSurfLevels(): array
    {
        return $this->faker->randomElements(
            SurfLevel::cases(),
            $this->faker->numberBetween(1, count(SurfLevel::cases()))
        );
    }

    private function randomTripTitle(string $location): string
    {
        return sprintf(
            '%s %s %s',
            $this->faker->randomElement(self::TRIP_TITLE_PREFIXES),
            $this->faker->randomElement(self::TRIP_TITLE_SUFFIXES),
            $location,
        );
    }

    /**
     * @return array<string, string|float>
     */
    private function getLocationData(string $locationLabel): array
    {
        $location = explode(',', $locationLabel)[0];

        return SurfTripData::ALL[$location]['location'];
    }
}
