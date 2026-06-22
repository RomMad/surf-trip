<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use App\Enum\User\SurfLevel;
use App\Factory\TripFactory;
use App\Form\Model\Trip\TripSearchInput;
use App\ReadModel\Trip\TripShowReadModel;
use App\Repository\TripRepository;
use App\Tests\CustomKernelTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\TripStory;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;

/**
 * @internal
 */
#[CoversClass(TripRepository::class)]
#[Medium]
final class TripRepositoryTest extends CustomKernelTestCase
{
    private ?TripRepository $repository = null;

    protected function setUp(): void
    {
        parent::setUp();

        DefaultStory::load();

        $this->repository = $this->getContainer()->get(TripRepository::class);
    }

    public function testSaveTrip(): void
    {
        $trip = TripFactory::new()->withoutPersisting()->create();

        $this->repository->save($trip, true);

        $this->assertSame($trip->title, $this->repository->findOneBy([], ['id' => 'DESC'])->title);
    }

    public function testRemoveTrip(): void
    {
        $trip = TripFactory::first();
        $tripId = $trip->id;

        $this->repository->remove($trip, true);

        $this->assertNull($this->repository->find($tripId));
    }

    public function testCreateOrderedQueryBuilderWithoutFilters(): void
    {
        $searchInput = new TripSearchInput();
        $trips = $this->getTrips($searchInput);

        $this->assertCount(21, $trips);
    }

    public function testCreateOrderedQueryBuilderWithSearchFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->query = TripStory::TRIP_TITLE;

        $trips = $this->getTrips($searchInput);

        $this->assertCount(1, $trips);
        $this->assertSame(TripStory::TRIP_TITLE, $trips[0]->title->value);
    }

    public function testCreateOrderedQueryBuilderWithLocationFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->location = TripStory::TRIP_LOCATION;

        $trips = $this->getTrips($searchInput);

        $this->assertCount(1, $trips);
        $this->assertSame(TripStory::TRIP_LOCATION, $trips[0]->location->label);
    }

    public function testCreateOrderedQueryBuilderWithSurfLevelsFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->requiredLevels = [SurfLevel::Intermediate];

        $trips = $this->getTrips($searchInput);

        $this->assertGreaterThan(1, count($trips));
        $this->assertContains(SurfLevel::Intermediate, $trips[0]->requiredLevels);
    }

    public function testCreateOrderedQueryBuilderWithCombinedFilters(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->query = 'Bali';
        $searchInput->location = 'Indonesia';
        $searchInput->requiredLevels = [SurfLevel::Beginner];
        $searchInput->myTripsOnly = true;

        $user = UserStory::getJohnUser();

        $trips = $this->getTrips($searchInput, $user);

        $this->assertCount(1, $trips);
    }

    public function testCreateOrderedQueryBuilderWithMyTripsOnlyFilterAndAuthenticatedUser(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->myTripsOnly = true;

        $user = UserStory::getJohnUser();

        $trips = $this->getTrips($searchInput, $user);

        $this->assertNotEmpty($trips);
        $this->assertCount($user->trips->count(), $trips);
    }

    public function testCreateOrderedQueryBuilderWithMyTripsOnlyFilterAndAnonymousUser(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->myTripsOnly = true;

        $tripCount = $this->getTripCount($searchInput);
        $countWithoutFilter = $this->getTripCount(new TripSearchInput());

        $this->assertSame($countWithoutFilter, $tripCount);
    }

    public function testGetCountQueryBuilderWithoutFilters(): void
    {
        $searchInput = new TripSearchInput();

        $tripCount = $this->getTripCount($searchInput);

        $this->assertSame(21, $tripCount);
    }

    public function testGetCountQueryBuilderWithSearchFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->query = TripStory::TRIP_TITLE;

        $tripCount = $this->getTripCount($searchInput);

        $this->assertSame(1, $tripCount);
    }

    public function testGetCountQueryBuilderWithLocationFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->location = 'Bali';

        $tripCount = $this->getTripCount($searchInput);

        $this->assertSame(1, $tripCount);
    }

    public function testGetCountQueryBuilderWithMyTripsOnlyFilterAndAuthenticatedUser(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->myTripsOnly = true;

        $user = UserStory::getJohnUser();

        $tripCount = $this->getTripCount($searchInput, $user);

        $this->assertGreaterThanOrEqual(1, $tripCount);
    }

    public function testFindShowReadModelByIdReturnsCorrectModel(): void
    {
        $trip = TripFactory::last();
        $result = $this->repository->findShowReadModelById($trip->id);

        $this->assertInstanceOf(TripShowReadModel::class, $result);
        $this->assertSame($trip->id, $result->id);
    }

    public function testFindShowReadModelByIdReturnsNullForNonExistentId(): void
    {
        $result = $this->repository->findShowReadModelById(999999);

        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->repository = null;
    }

    /**
     * @return TripShowReadModel[]
     */
    private function getTrips(TripSearchInput $searchInput, ?User $user = null): array
    {
        return $this->repository
            ->createOrderedQueryBuilder($searchInput, $user)
            ->getQuery()
            ->getResult()
        ;
    }

    private function getTripCount(TripSearchInput $searchInput, ?User $user = null): int
    {
        return (int) $this->repository
            ->getCountQueryBuilder($searchInput, $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
