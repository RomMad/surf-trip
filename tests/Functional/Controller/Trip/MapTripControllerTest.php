<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\TripStory;
use App\Turbo\Frame\TripFrameId;
use App\Turbo\Http\TurboHeader;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Medium]
final class MapTripControllerTest extends CustomWebTestCase
{
    private const string PATH = '/en/trips/map';
    private const string TITLE = 'Trips';
    private const string MAP_SELECTOR = '[data-controller="symfony--ux-leaflet-map--map"]';
    private const string RESULTS_COUNT_SELECTOR = '#results_count';

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);
    }

    public function testMapTripPageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
        $this->assertSelectorExists(self::MAP_SELECTOR);
        $this->assertSelectorTextSame(self::RESULTS_COUNT_SELECTOR, '21 results');
    }

    public function testMapTripPageWithTurboFrameIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, server: [
            TurboHeader::FRAME_SERVER => TripFrameId::RESULTS,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists(self::MAP_SELECTOR);
        $this->assertSelectorTextSame(self::RESULTS_COUNT_SELECTOR, '21 results');
    }

    public function testMapTripPageCanBeFilteredByQuery(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'query' => TripStory::TRIP_TITLE,
        ], server: [
            TurboHeader::FRAME_SERVER => TripFrameId::RESULTS,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::RESULTS_COUNT_SELECTOR, '1 result');
    }

    public function testMapTripPageCanBeFilteredByLocation(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'location' => TripStory::TRIP_LOCATION,
        ], server: [
            TurboHeader::FRAME_SERVER => TripFrameId::RESULTS,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::RESULTS_COUNT_SELECTOR, '1 result');
    }
}
