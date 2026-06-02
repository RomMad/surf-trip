<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Controller\User\UserAutocompleteController;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(UserAutocompleteController::class)]
#[Medium]
final class UserAutocompleteControllerTest extends CustomWebTestCase
{
    private const string PATH = '/autocomplete/users/search';

    private const string UNKNOWN_USER_NAME = 'Unknown user name';
    private const string EXISTING_USER_NAME = 'john doe';

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class, UserStory::JOHN_EMAIL);
    }

    public function testAutocompleteReturnsEmptyResultsWhenNoUserMatchesQuery(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'query' => 'Unknown user name',
        ]);

        $content = $this->getJsonContent();

        $this->assertResponseIsSuccessful();
        $this->assertSame([], $content['results']);
    }

    public function testAutocompleteReturnsMatchingUser(): void
    {
        $this->client->request(Request::METHOD_GET, self::PATH, [
            'query' => self::EXISTING_USER_NAME,
        ]);

        $content = $this->getJsonContent();
        $results = $content['results'] ?? [];

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $results);
        $this->assertStringContainsString(UserStory::JOHN_FULL_NAME, $results[0]['text']);
    }
}
