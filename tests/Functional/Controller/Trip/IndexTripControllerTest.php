<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Trip;

use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Attribute\ResetDatabase;

/**
 * @internal
 */
#[ResetDatabase]
final class IndexTripControllerTest extends CustomWebTestCase
{
    private const string PATH = '/trips';
    private const string TITLE = 'Trips';

    protected function setUp(): void
    {
        $this->setUpTest(DefaultStory::class, self::JOHN_USER);
    }

    public function testIndexTripPageIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame(self::TITLE_H1, self::TITLE);
        $this->assertSelectorExists(self::TABLE);
        $this->assertSelectorCount(10, self::TABLE_ROW);
    }

    public function testIndexTripPageWithTurboFrameIsDisplayed(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, server: [
            'HTTP_Turbo-Frame' => 'trip_results',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists(self::TITLE_H1);
        $this->assertSelectorExists(self::TABLE);
        $this->assertSelectorCount(10, self::TABLE_ROW);
    }
}
