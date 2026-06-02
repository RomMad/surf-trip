<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Entity\Trip;
use App\Factory\TripFactory;
use App\Service\Trip\TripReadModelProvider;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Traits\CalendarLinksTestTrait;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[Medium]
final class ShowTripControllerTest extends CustomWebTestCase
{
    use CalendarLinksTestTrait;

    private const string PATH = '/en/trip/%d/%s';
    private const string TITLE = 'Trip';
    private const string ADD_SESSION_LABEL = 'Add session';

    private ?Trip $trip = null;

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);

        $this->trip = TripFactory::last();
    }

    public function testShowTripPageIsDisplayed(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH, $this->trip->id, $this->trip->slug->value)
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
        $this->assertSelectorExists(self::TABLE);
        $this->assertSelectorTextContains(self::TABLE, $this->trip->title->value);
        $this->assertCount(1, $this->client->getCrawler()->selectLink(self::ADD_SESSION_LABEL));
    }

    public function testShowTripPageRedirectsWhenSlugIsInvalid(): void
    {
        $this->client->followRedirects(false);

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH, $this->trip->id, 'invalid-slug')
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);
        $this->assertResponseRedirects(sprintf(
            self::PATH,
            $this->trip->id,
            $this->trip->slug->value
        ));
    }

    public function testShowTripIsNotFound(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH, 9999, 'non-existing-trip')
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCalendarLinksCanBeClickedFromTheTripPage(): void
    {
        $tripReadModelProvider = $this->getContainer()->get(TripReadModelProvider::class);
        $tripReadModel = $tripReadModelProvider->getById($this->trip->id);

        $this->assertCalendarLinksCanBeClicked(
            $tripReadModel,
            sprintf(self::PATH, $this->trip->id, $this->trip->slug->value)
        );
    }
}
