<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\SurfSession;

use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\SurfSessionStory;
use App\Turbo\Frame\SurfSessionFrameId;
use App\Turbo\Http\TurboContentType;
use App\Turbo\Http\TurboHeader;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Medium]
final class IndexSurfSessionControllerTest extends CustomWebTestCase
{
    private const string PATH = '/en/sessions';
    private const string TITLE = 'Sessions';
    private const string CARD = '.app-card';

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);
    }

    public function testIndexSurfSessionPageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
        $this->assertSelectorCount(10, self::CARD);
    }

    public function testIndexSurfSessionPageCanBeFilteredByQuery(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'query' => SurfSessionStory::SPOT,
        ], server: [
            TurboHeader::FRAME_SERVER => SurfSessionFrameId::RESULTS,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(1, self::CARD);
    }

    public function testIndexSurfSessionPageCanBeFilteredByDate(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'period' => [
                'from' => new \DateTimeImmutable('-1 day')->format('Y-m-d'),
                'to' => new \DateTimeImmutable('now')->format('Y-m-d'),
            ],
        ], server: [
            TurboHeader::FRAME_SERVER => SurfSessionFrameId::RESULTS,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(1, self::CARD);
    }

    public function testInfiniteScrollCanLoadTheNextPageInTurboFrame(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'page' => 2,
        ], server: [
            TurboHeader::FRAME_SERVER => SurfSessionFrameId::LIST,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', TurboContentType::STREAM_HTML_UTF8);
        $this->assertSelectorExists(self::CARD);
    }
}
