<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\RequiredLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class TripFixtures extends Fixture implements DependentFixtureInterface
{
    private const int RANDOM_TRIPS_COUNT = 100;
    private const int USER_REFERENCE_COUNT = 5;

    private const array TRIPS_DATA = [
        [
            'title' => 'Summer Surf Adventure Bali',
            'location' => 'Bali, Indonesia',
            'startAt' => '2025-06-01',
            'endAt' => '2025-06-15',
            'requiredLevels' => [RequiredLevel::BEGINNER, RequiredLevel::INTERMEDIATE],
            'description' => 'Experience the best waves of Bali with experienced instructors.
                Perfect for beginners looking to improve their skills.',
            'owners' => [0, 1],
        ],
        [
            'title' => 'Advanced Surfing in Portugal',
            'location' => 'Nazaré, Portugal',
            'startAt' => '2025-07-10',
            'endAt' => '2025-07-20',
            'requiredLevels' => [RequiredLevel::ADVANCED],
            'description' => 'Challenge yourself with some of the biggest waves in the world.
                Only for experienced surfers.',
            'owners' => [1],
        ],
        [
            'title' => 'Tropical Waves Hawaii',
            'location' => 'Honolulu, Hawaii',
            'startAt' => '2025-08-01',
            'endAt' => '2025-08-10',
            'requiredLevels' => [RequiredLevel::INTERMEDIATE],
            'description' => 'Ride the iconic waves of Hawaii with breathtaking views.
                Great for intermediate surfers wanting to explore new breaks.',
            'owners' => [2, 3],
        ],
        [
            'title' => 'Costa Rica Epic Journey',
            'location' => 'Guanacaste, Costa Rica',
            'startAt' => '2025-09-05',
            'endAt' => '2025-09-18',
            'requiredLevels' => [RequiredLevel::BEGINNER, RequiredLevel::INTERMEDIATE, RequiredLevel::ADVANCED],
            'description' => 'A comprehensive surf trip with waves for all levels.
                Explore multiple breaks and tropical landscapes.',
            'owners' => [0, 2],
        ],
        [
            'title' => 'Beginner Paradise Fuerteventura',
            'location' => 'Fuerteventura, Spain',
            'startAt' => '2025-05-15',
            'endAt' => '2025-05-25',
            'requiredLevels' => [RequiredLevel::BEGINNER],
            'description' => 'Perfect destination for learning to surf.
                Calm, consistent waves and beautiful beaches.',
            'owners' => [3, 4],
        ],
        [
            'title' => 'Winter Swell of Tahiti',
            'location' => "Teahupo'o, Tahiti",
            'startAt' => '2025-11-01',
            'endAt' => '2025-11-12',
            'requiredLevels' => [RequiredLevel::ADVANCED],
            'description' => "Experience the legendary barrel at Teahupo'o.
                For expert surfers only.",
            'owners' => [1, 4],
        ],
        [
            'title' => 'Australian East Coast Explorer',
            'location' => 'Byron Bay, Australia',
            'startAt' => '2025-10-20',
            'endAt' => '2025-10-30',
            'requiredLevels' => [RequiredLevel::BEGINNER, RequiredLevel::INTERMEDIATE],
            'description' => 'Discover the vibrant surf culture of East Australia.
                Multiple breaks suitable for various skill levels.',
            'owners' => [0],
        ],
        [
            'title' => 'Summer Sessions Morocco',
            'location' => 'Essaouira, Morocco',
            'startAt' => '2025-07-01',
            'endAt' => '2025-07-08',
            'requiredLevels' => [RequiredLevel::INTERMEDIATE],
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
        foreach (self::TRIPS_DATA as $tripData) {
            $trip = new Trip()
                ->setTitle($tripData['title'])
                ->setLocation($tripData['location'])
                ->setStartAt(new \DateTimeImmutable($tripData['startAt']))
                ->setEndAt(new \DateTimeImmutable($tripData['endAt']))
                ->setRequiredLevels($tripData['requiredLevels'])
                ->setDescription($tripData['description'])
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker->dateTimeBetween('-1 month', 'today')))
            ;

            foreach ($tripData['owners'] as $ownerIndex) {
                /** @var User $owner */
                $owner = $this->getReference(UserFixtures::USER_REFERENCE.$ownerIndex, User::class);
                $trip->addOwner($owner);
            }

            yield $trip;
        }

        for ($index = 0; $index < self::RANDOM_TRIPS_COUNT; ++$index) {
            /** @var \DateTimeInterface $randomStart */
            $randomStart = $this->faker->dateTimeBetween('-6 months', '+6 months');
            $startAt = \DateTimeImmutable::createFromInterface($randomStart);

            $trip = new Trip()
                ->setTitle(sprintf('%s Surf Trip', ucfirst((string) $this->faker->words($this->faker->numberBetween(2, 4), true))))
                ->setLocation(sprintf('%s, %s', $this->faker->city(), $this->faker->country()))
                ->setStartAt($startAt)
                ->setEndAt($startAt->modify(sprintf('+%d days', $this->faker->numberBetween(3, 14))))
                ->setRequiredLevels($this->randomRequiredLevels())
                ->setDescription($this->faker->paragraphs($this->faker->numberBetween(1, 3), true))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker->dateTimeBetween('-1 year', '-1 month')))
            ;

            /** @var list<int> $owners */
            $owners = $this->faker->randomElements(range(0, self::USER_REFERENCE_COUNT - 1), $this->faker->numberBetween(1, 2));

            foreach ($owners as $ownerIndex) {
                /** @var User $owner */
                $owner = $this->getReference(UserFixtures::USER_REFERENCE.$ownerIndex, User::class);
                $trip->addOwner($owner);
            }

            yield $trip;
        }
    }

    /**
     * @return list<RequiredLevel>
     */
    private function randomRequiredLevels(): array
    {
        return $this->faker->randomElements(
            RequiredLevel::cases(),
            $this->faker->numberBetween(1, count(RequiredLevel::cases()))
        );
    }
}
