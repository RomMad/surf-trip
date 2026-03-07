<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Controller\Trip\ShowTripController;
use App\Entity\Trip;
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
#[CoversClass(ShowTripController::class)]
#[Medium]
final class ShowTripControllerTest extends CustomWebTestCase
{
    private const string PATH = '/en/trip/%d/%s';
    private const string TITLE = 'Trip';

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
}
