<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Data\SurfTripData;
use App\Entity\SurfSession;
use App\Entity\Trip;
use App\Enum\SurfSession\SurfSessionDuration;
use App\Enum\SurfSession\SurfSessionRating;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class SurfSessionFixtures extends Fixture implements DependentFixtureInterface
{
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
        'Practice backside turns in steeper sections',
        'Work on generating speed in weaker sections',
        'Trim and nose-riding on cleaner right-handers',
        'Stay consistent on late drops',
        'Keep paddling rhythm and improve wave selection',
        'Improve timing on take-offs',
        'Work on bottom turns and rail engagement',
        'Practice cutbacks with better flow',
        'Focus on positioning and wave reading',
        'Improve pop-up speed and stability',
        'Build confidence in steeper waves',
        'Practice duck dives in stronger surf',
        'Improve frontside carving technique',
        'Work on linking maneuvers more smoothly',
        'Stay relaxed during bigger sets',
        'Improve endurance and paddling efficiency',
        'Focus on cleaner turns and body rotation',
        'Practice surfing closer to the pocket',
        'Improve consistency on smaller waves',
        'Work on speed generation without pumping too much',
    ];

    private const array COMMENTS = [
        'Clean waves early, light offshore wind, good energy in the water.',
        'Crowded peak but managed to catch quality waves on the shoulder.',
        'Tide dropped quickly, had to adjust positioning every 15 minutes.',
        'Strong paddling session, felt more stable on late take-offs.',
        'Small but fun conditions, perfect for technique-focused practice.',
        'Powerful sets and strong current, physically demanding but rewarding.',
        'Solid chest-high waves with clean faces at first light.',
        'Soft evening swell, fun walls but inconsistent sets.',
        'Long peeling rights and almost no crowd at sunrise.',
        'Wind picked up early and made sections bumpy and difficult.',
        'Very small and mushy surf, little power to work with.',
        'Good mix of push and shape, nice progression through the session.',
        'Fun shoulder-high sets with glassy conditions all morning.',
        'Crowded lineup but managed to catch a few clean waves.',
        'Messy conditions with shifting peaks, hard to find rhythm.',
        'Super clean offshore conditions with long workable walls.',
        'Small but playful surf, perfect for practicing technique.',
        'Heavy closeouts today, difficult to find good sections.',
        'Consistent swell with plenty of opportunities to improve.',
        'Late afternoon session with beautiful light and mellow crowd.',
        'Powerful waves but tricky timing on the take-off.',
        'Better conditions than expected, smooth faces and fun sections.',
        'Choppy surface due to wind but still some enjoyable rides.',
        'Long lulls between sets, patience required.',
        'Excellent energy in the water and steady improvement throughout.',
        'Tiring paddle-out but rewarding once outside.',
    ];

    private readonly Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        /** @var list<Trip> $trips */
        $trips = $manager->getRepository(Trip::class)->findAll();

        if ([] === $trips) {
            throw new \LogicException('No trip found. Load TripFixtures before SurfSessionFixtures.');
        }

        foreach ($trips as $trip) {
            foreach ($this->generateSurfSessions($trip) as $surfSession) {
                $manager->persist($surfSession);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TripFixtures::class,
        ];
    }

    /**
     * @return \Generator<SurfSession>
     */
    private function generateSurfSessions(Trip $trip): \Generator
    {
        $currentDate = $trip->startAt->setTime(0, 0);
        $endDate = $trip->endAt->setTime(0, 0);
        $spots = $this->getSpotsForTrip($trip);

        while ($currentDate <= $endDate) {
            foreach ($trip->owners as $owner) {
                for ($i = 0; $i < $this->faker->numberBetween(0, 2); ++$i) {
                    [$startAt, $endAt] = $this->buildSessionDatesForDay($currentDate, $i);

                    $surfSession = new SurfSession();
                    $surfSession->spot = $this->faker->randomElement($spots);
                    $surfSession->startAt = $startAt;
                    $surfSession->endAt = $endAt;
                    $surfSession->trip = $trip;
                    $surfSession->user = $owner;
                    $surfSession->board = $this->faker->optional(0.8)->randomElement(self::BOARDS);
                    $surfSession->rating = $this->faker->optional(0.9)->randomElement(SurfSessionRating::cases());
                    $surfSession->objective = $this->faker->optional(0.7)->randomElement(self::OBJECTIVES);
                    $surfSession->comment = $this->faker->optional(0.8)->randomElement(self::COMMENTS);

                    yield $surfSession;
                }
            }

            $currentDate = $currentDate->modify('+1 day');
        }
    }

    /**
     * @return array<int, string>
     */
    private function getSpotsForTrip(Trip $trip): array
    {
        $location = explode(',', (string) $trip->location->label)[0];

        return SurfTripData::ALL[$location]['spots'];
    }

    /**
     * @return array{0: \DateTimeImmutable, 1: \DateTimeImmutable}
     */
    private function buildSessionDatesForDay(\DateTimeImmutable $date, int $sessionIndex): array
    {
        $startHour = 0 === $sessionIndex ? $this->faker->numberBetween(6, 10) : $this->faker->numberBetween(14, 18);
        $startMinute = $this->faker->randomElement([0, 30]);
        $startAt = $date->setTime($startHour, $startMinute);

        $durationMinutes = $this->faker->randomElement([
            SurfSessionDuration::Minutes60,
            SurfSessionDuration::Minutes90,
            SurfSessionDuration::Minutes120,
            SurfSessionDuration::Minutes150,
            SurfSessionDuration::Minutes180,
        ])->value;

        $endAt = $startAt->modify(sprintf('+%d minutes', $durationMinutes));

        return [$startAt, $endAt];
    }
}
