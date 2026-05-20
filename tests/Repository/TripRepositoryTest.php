<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Enum\User\SurfLevel;
use App\Factory\TripFactory;
use App\Form\Model\Trip\TripSearchInput;
use App\ReadModel\Trip\TripShowReadModel;
use App\Repository\TripRepository;
use App\Tests\CustomKernelTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\TripStory;
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
        $queryBuilder = $this->repository->createOrderedQueryBuilder($searchInput);
        $results = $queryBuilder->getQuery()->getResult();

        $this->assertCount(21, $results);
        $this->assertContainsOnlyInstancesOf(TripShowReadModel::class, $results);
    }

    public function testCreateOrderedQueryBuilderWithSearchFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->query = TripStory::TRIP_TITLE;

        $results = $this->repository
            ->createOrderedQueryBuilder($searchInput)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $results);
        $this->assertSame(TripStory::TRIP_TITLE, $results[0]->title->value);
    }

    public function testCreateOrderedQueryBuilderWithLocationFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->location = TripStory::TRIP_LOCATION;

        $results = $this->repository
            ->createOrderedQueryBuilder($searchInput)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $results);
        $this->assertSame(TripStory::TRIP_LOCATION, $results[0]->location->value);
    }

    public function testCreateOrderedQueryBuilderWithSurfLevelsFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->requiredLevels = [SurfLevel::Intermediate];

        $results = $this->repository
            ->createOrderedQueryBuilder($searchInput)
            ->getQuery()
            ->getResult()
        ;

        $this->assertGreaterThan(1, count($results));
        $this->assertContains(SurfLevel::Intermediate, $results[0]->requiredLevels);
    }

    public function testCreateOrderedQueryBuilderWithCombinedFilters(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->query = 'Bali';
        $searchInput->location = 'Indonesia';
        $searchInput->requiredLevels = [SurfLevel::Beginner];

        $results = $this->repository
            ->createOrderedQueryBuilder($searchInput)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $results);
    }

    public function testGetCountQueryBuilderWithoutFilters(): void
    {
        $searchInput = new TripSearchInput();

        $count = (int) $this->repository
            ->getCountQueryBuilder($searchInput)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(21, $count);
    }

    public function testGetCountQueryBuilderWithSearchFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->query = TripStory::TRIP_TITLE;

        $count = (int) $this->repository
            ->getCountQueryBuilder($searchInput)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(1, $count);
    }

    public function testGetCountQueryBuilderWithLocationFilter(): void
    {
        $searchInput = new TripSearchInput();
        $searchInput->location = 'Bali';

        $count = (int) $this->repository
            ->getCountQueryBuilder($searchInput)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(1, $count);
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
}
