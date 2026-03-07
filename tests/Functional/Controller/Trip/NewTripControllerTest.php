<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Enum\Trip\RequiredLevel;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class NewTripControllerTest extends CustomWebTestCase
{
    // Paths
    private const string PATH_INDEX = '/trips';
    private const string PATH_NEW = '/trip/new';
    // Selectors
    private const string FORM = 'form[name="trip"]';
    // Labels
    private const string TITLE = 'New trip';
    private const string NEW_TRIP_BUTTON = 'Create new';
    private const string SUBMIT_BUTTON = 'Save';
    // Messages
    private const string MESSAGE_SUCCESS = 'The trip has been created.';
    // Data
    private const string TRIP_TITLE = 'Surf Trip Test';
    private const string TRIP_LOCATION = 'Biarritz';

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);
    }

    public function testNewTripPageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH_INDEX);
        $this->client->clickLink(self::NEW_TRIP_BUTTON);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists(self::FORM);
    }

    public function testCreateTripIsSuccessful(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH_NEW);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);

        $this->client->submitForm(self::SUBMIT_BUTTON, [
            'trip[title]' => self::TRIP_TITLE,
            'trip[location]' => self::TRIP_LOCATION,
            'trip[startAt]' => new \DateTimeImmutable('+1 month')->format(self::FORMAT_DATETIME),
            'trip[endAt]' => new \DateTimeImmutable('+1 month +1 week')->format(self::FORMAT_DATETIME),
            'trip[requiredLevels]' => [RequiredLevel::Beginner->value],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertAlertSuccessExists();
        $this->assertSelectorTextContains(self::ALERT_SUCCESS, self::MESSAGE_SUCCESS);
        $this->assertSelectorTextContains(self::FIRST_ROW, self::TRIP_TITLE);
    }
}
