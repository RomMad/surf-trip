<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Entity\Trip;
use App\Entity\ValueObject\Title;
use App\Factory\SurfSessionFactory;
use App\Factory\TripFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\TripStory;
use App\Tests\Fixtures\UserStory;
use App\Turbo\Frame\TripFrameId;
use App\Turbo\Http\TurboContentType;
use App\Turbo\Http\TurboHeader;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Medium]
final class ShowTripSurfSessionSectionControllerTest extends CustomWebTestCase
{
    private const string PATH = '/en/trip/%d/surf-sessions/section';
    private const string SECTION_TITLE = 'My surf sessions';
    private const string ADD_SESSION_LABEL = 'Add session';

    private ?Trip $trip = null;

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);

        $this->trip = TripFactory::find(['title' => Title::from(TripStory::CURRENT_TRIP_TITLE)]);

        SurfSessionFactory::createMany(8, [
            'user' => UserStory::getJohnUser(),
            'trip' => $this->trip,
        ]);
    }

    public function testTripSurfSessionSectionFragmentIsDisplayed(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH, $this->trip->id),
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('section', self::SECTION_TITLE);
        $this->assertCount(1, $this->client->getCrawler()->selectLink(self::ADD_SESSION_LABEL));
        $this->assertSelectorCount(6, self::CARD);
        $this->assertSelectorExists(sprintf(
            'turbo-frame#%s',
            TripFrameId::SURF_SESSIONS_LIST,
        ));
    }

    public function testInfiniteScrollCanLoadTheNextPageInTurboFrame(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH, $this->trip->id),
            ['page' => 2],
            server: [
                TurboHeader::FRAME_SERVER => TripFrameId::SURF_SESSIONS_LIST,
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', TurboContentType::STREAM_HTML_UTF8);
        $this->assertSelectorExists(self::CARD);
        $this->assertSelectorCount(3, self::CARD);
    }
}
