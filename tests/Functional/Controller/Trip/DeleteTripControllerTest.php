<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Controller\Trip\DeleteTripController;
use App\Entity\Trip;
use App\Factory\TripFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(DeleteTripController::class)]
#[Medium]
final class DeleteTripControllerTest extends CustomWebTestCase
{
    private const string PATH_SHOW = '/en/trip/%d/%s';
    private const string SUBMIT_BUTTON = 'Delete';
    private const string MESSAGE_SUCCESS = 'The trip has been deleted.';

    private ?Trip $trip = null;

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);

        $this->trip = TripFactory::last();
    }

    public function testDeleteTripIsSuccessful(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PATH_SHOW, $this->trip->id, $this->trip->slug->value)
        );

        $this->assertResponseIsSuccessful();

        $this->client->submitForm(self::SUBMIT_BUTTON);

        $trip = $this->getRepository(Trip::class)->find($this->trip->id);

        $this->assertResponseIsSuccessful();
        $this->assertAlertSuccessExists();
        $this->assertSelectorTextContains(self::ALERT_SUCCESS, self::MESSAGE_SUCCESS);
        $this->assertNull($trip);
    }
}
