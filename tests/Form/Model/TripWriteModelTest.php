<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ValueObject\Location;
use App\Entity\ValueObject\Title;
use App\Enum\Trip\RequiredLevel;
use App\Factory\TripFactory;
use App\Form\Model\TripWriteModel;
use App\ReadModel\Trip\TripOwnerReadModel;
use App\Tests\CustomKernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
#[CoversClass(TripWriteModel::class)]
#[Small]
final class TripWriteModelTest extends CustomKernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testInvalidTrip(): void
    {
        $trip = new TripWriteModel();

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

    private function createValidTrip(): TripWriteModel
    {
        $trip = TripFactory::createOne();

        $trip = new TripWriteModel();
        $trip->title = new Title('Test Trip');
        $trip->location = new Location('Test Location');
        $trip->startAt = new \DateTimeImmutable('+1 week');
        $trip->endAt = new \DateTimeImmutable('+2 weeks');
        $trip->requiredLevels = [RequiredLevel::Beginner];
        $trip->description = 'Test Description';
        $trip->owners = [new TripOwnerReadModel(1, 'Test Owner')];

        return $trip;
    }
}
