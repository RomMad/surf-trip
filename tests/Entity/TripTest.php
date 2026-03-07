<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Trip;
use App\Factory\TripFactory;
use App\Tests\CustomKernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
#[CoversClass(Trip::class)]
#[Small]
final class TripTest extends CustomKernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testInvalidTrip(): void
    {
        $trip = new Trip();

        $violations = $this->validator->validate($trip);

        $this->assertCount(4, $violations);
    }

    public function testValidTrip(): void
    {
        $trip = $this->createValidTrip();

        $violations = $this->validator->validate($trip);

        $this->assertCount(0, $violations);
    }

    public function testInvalidTripWithEmptyStartDate(): void
    {
        $trip = $this->createValidTrip();
        $trip->startAt = null;

        $violations = $this->validator->validate($trip);

        $this->assertCount(1, $violations);
    }

    public function testInvalidTripWithEndDateBeforeStartDate(): void
    {
        $trip = $this->createValidTrip();
        $trip->startAt = new \DateTimeImmutable('+1 week');
        $trip->endAt = new \DateTimeImmutable('+1 week -1 day');

        $violations = $this->validator->validate($trip);

        $this->assertCount(1, $violations);
    }

    public function testInvalidTripWithEmptyRequiredLevels(): void
    {
        $trip = $this->createValidTrip();
        $trip->requiredLevels = [];

        $violations = $this->validator->validate($trip);

        $this->assertCount(1, $violations);
    }

    private function createValidTrip(): Trip
    {
        return TripFactory::new()->withoutPersisting()->create();
    }
}
