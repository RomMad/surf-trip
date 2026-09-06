<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Trip;
use App\Tests\CustomKernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;

/**
 * @internal
 */
#[CoversClass(Trip::class)]
#[Small]
final class TripTest extends CustomKernelTestCase
{
    public function testPublishTripSetsPublishedAt(): void
    {
        $trip = new Trip();
        $before = new \DateTimeImmutable();

        $trip->publish();

        $this->assertTrue($trip->isPublished());
        $this->assertGreaterThanOrEqual($before, $trip->publishedAt);
    }

    public function testPublishTripTwiceDoesNotChangePublishedAt(): void
    {
        $trip = new Trip();
        $trip->publish();

        $firstPublishedAt = $trip->publishedAt;

        $trip->publish();

        $this->assertSame($firstPublishedAt, $trip->publishedAt);
    }

    public function testUnpublishTripResetsPublishedAt(): void
    {
        $trip = new Trip();
        $trip->publish();

        $this->assertTrue($trip->isPublished());

        $trip->unpublish();

        $this->assertFalse($trip->isPublished());
        $this->assertNull($trip->publishedAt);
    }
}
