<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\TripStory;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Medium]
final class TripAutocompleteControllerTest extends CustomWebTestCase
{
    private const string PATH = '/autocomplete/trips/search';

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);
    }

    public function testAutocompleteDoesNotReturnFutureTripWithDefaultReferenceAt(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'query' => TripStory::TRIP_TITLE,
        ]);

        $this->assertResponseIsSuccessful();

        $content = $this->getJsonContent();

        $this->assertSame([], $content['results']);
    }

    public function testAutocompleteReturnsFutureTripWithReferenceAtFromExtraOptions(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'query' => TripStory::TRIP_TITLE,
            'extra_options' => $this->encodeExtraOptions([
                'reference_at' => new \DateTimeImmutable('+1 month +1 day')->format(\DateTimeInterface::ATOM),
            ]),
        ]);

        $this->assertResponseIsSuccessful();

        $content = $this->getJsonContent();

        $this->assertCount(1, $content['results']);
        $this->assertStringContainsString(TripStory::TRIP_TITLE, $content['results'][0]['text']);
    }

    /**
     * @param array<string, scalar> $extraOptions
     */
    private function encodeExtraOptions(array $extraOptions): string
    {
        return base64_encode((string) json_encode($extraOptions, \JSON_THROW_ON_ERROR));
    }
}
