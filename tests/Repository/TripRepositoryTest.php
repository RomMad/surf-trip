<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Enum\Trip\RequiredLevel;
use App\Factory\TripFactory;
use App\Form\Model\TripFilter;
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
        DefaultStory::load();

        $this->repository = $this->getContainer()->get(TripRepository::class);
    }

    public function testSaveTrip(): void
    {
        $trip = TripFactory::createOne();

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
        $filter = new TripFilter();
        $queryBuilder = $this->repository->createOrderedQueryBuilder($filter);
        $results = $queryBuilder->getQuery()->getResult();

        $this->assertCount(21, $results);
        $this->assertContainsOnlyInstancesOf(TripShowReadModel::class, $results);
    }

    public function testCreateOrderedQueryBuilderWithSearchFilter(): void
    {
        $filter = new TripFilter();
        $filter->search = TripStory::TRIP_TITLE;

        $results = $this->repository
            ->createOrderedQueryBuilder($filter)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $results);
        $this->assertSame(TripStory::TRIP_TITLE, $results[0]->title->value);
    }

    public function testCreateOrderedQueryBuilderWithLocationFilter(): void
    {
        $filter = new TripFilter();
        $filter->location = TripStory::TRIP_LOCATION;

        $results = $this->repository
            ->createOrderedQueryBuilder($filter)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $results);
        $this->assertSame(TripStory::TRIP_LOCATION, $results[0]->location->value);
    }

    public function testCreateOrderedQueryBuilderWithRequiredLevelsFilter(): void
    {
        $filter = new TripFilter();
        $filter->requiredLevels = [RequiredLevel::Intermediate];

        $results = $this->repository
            ->createOrderedQueryBuilder($filter)
            ->getQuery()
            ->getResult()
        ;

        $this->assertGreaterThan(1, count($results));
        $this->assertContains(RequiredLevel::Intermediate, $results[0]->requiredLevels);
    }

    public function testCreateOrderedQueryBuilderWithCombinedFilters(): void
    {
        $filter = new TripFilter();
        $filter->search = 'Bali';
        $filter->location = 'Indonesia';
        $filter->requiredLevels = [RequiredLevel::Beginner];

        $results = $this->repository
            ->createOrderedQueryBuilder($filter)
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $results);
    }

    public function testGetCountQueryBuilderWithoutFilters(): void
    {
        $filter = new TripFilter();

        $count = (int) $this->repository
            ->getCountQueryBuilder($filter)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(21, $count);
    }

    public function testGetCountQueryBuilderWithSearchFilter(): void
    {
        $filter = new TripFilter();
        $filter->search = TripStory::TRIP_TITLE;

        $count = (int) $this->repository
            ->getCountQueryBuilder($filter)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(1, $count);
    }

    public function testGetCountQueryBuilderWithLocationFilter(): void
    {
        $filter = new TripFilter();
        $filter->location = 'Bali';

        $count = (int) $this->repository
            ->getCountQueryBuilder($filter)
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
