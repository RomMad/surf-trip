<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\SurfSession;

use App\Controller\SurfSession\IndexSurfSessionController;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DefaultStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(IndexSurfSessionController::class)]
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
}
