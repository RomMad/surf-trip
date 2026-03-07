<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Controller\Trip\EditTripController;
use App\Entity\Trip;
use App\Enum\Trip\RequiredLevel;
use App\Factory\TripFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[CoversClass(EditTripController::class)]
#[Medium]
final class EditTripControllerTest extends CustomWebTestCase
{
    // Paths
    private const string PATH = '/en/trip/%d/%s/edit';
    // Selectors
    private const string FORM = 'form[name="trip"]';
    // Labels
    private const string TITLE = 'Edit trip';
    private const string SUBMIT_BUTTON = 'Update';
    // Messages
    private const string MESSAGE_SUCCESS = 'The trip has been updated.';
    // Data
    private const string UPDATED_TRIP_TITLE = 'Updated Surf Trip';
    private const string UPDATED_TRIP_LOCATION = 'Hossegor';

    private ?Trip $trip = null;

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);

        $this->trip = TripFactory::last();
    }

    public function testEditTripPageIsDisplayed(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH, $this->trip->id, $this->trip->slug)
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
        $this->assertSelectorExists(self::FORM);
    }

    public function testEditTripPageRedirectsWhenSlugIsInvalid(): void
    {
        $this->client->followRedirects(false);
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH, $this->trip->id, 'invalid-slug')
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);
        $this->assertResponseRedirects(sprintf(self::PATH, $this->trip->id, $this->trip->slug));
    }

    public function testEditTripIsSuccessful(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH, $this->trip->id, $this->trip->slug)
        );

        $this->assertResponseIsSuccessful();

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            'trip[title]' => self::UPDATED_TRIP_TITLE,
            'trip[location]' => self::UPDATED_TRIP_LOCATION,
            'trip[startAt]' => new \DateTimeImmutable('+2 month')->format(self::FORMAT_DATETIME),
            'trip[endAt]' => new \DateTimeImmutable('+2 month +1 week')->format(self::FORMAT_DATETIME),
            'trip[requiredLevels]' => [RequiredLevel::Intermediate->value],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertAlertSuccessExists();
        $this->assertSelectorTextContains(self::ALERT_SUCCESS, self::MESSAGE_SUCCESS);
        $this->assertSelectorTextContains(self::TABLE, self::UPDATED_TRIP_TITLE);
        $this->assertSelectorTextContains(self::TABLE, self::UPDATED_TRIP_LOCATION);

        $updatedTrip = $this->getRepository(Trip::class)->find($this->trip->id);

        $this->assertInstanceOf(Trip::class, $updatedTrip);
        $this->assertSame(self::UPDATED_TRIP_TITLE, $updatedTrip->title->value);
        $this->assertSame(self::UPDATED_TRIP_LOCATION, $updatedTrip->location->value);
    }
}
