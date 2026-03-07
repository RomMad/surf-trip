<?php

declare(strict_types=1);

namespace App\Tests;

use App\Factory\UserFactory;
use App\Tests\Traits\AssertsWebTestTrait;
use App\Tests\Traits\KernelTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;
use Zenstruck\Foundry\Story;

/**
 * @internal
 */
#[ResetDatabase]
abstract class CustomWebTestCase extends WebTestCase
{
    use AssertsWebTestTrait;
    use KernelTestCaseTrait;

    // Data
    protected const string JOHN_USER = 'john.doe@test.com';
    // Selectors
    protected const string TITLE_H1 = 'h1';
    protected const string TABLE = 'table';
    protected const string TABLE_ROW = 'table>tbody>tr';
    protected const string FIRST_ROW = 'table>tbody>tr:first-child';
    // Others
    protected const string FORMAT_DATETIME = 'Y-m-d\TH:00';

    protected ?KernelBrowser $client = null;

    /**
     * @param array<class-string<Story>>|class-string<Story> $stories
     */
    protected function setUpTest(array|string $stories = [], ?string $username = null, bool $clearCache = true): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->followRedirects();

        if (is_string($stories)) {
            $stories = [$stories];
        }

        if ([] !== $stories) {
            foreach ($stories as $story) {
                $story::load();
            }
        }

        if (null !== $username) {
            $user = UserFactory::find(['email' => $username]);
            $this->client->loginUser($user);
        }

        if ($clearCache) {
            $this->clearCache();
        }
    }

    /**
     * @param string $formName id or name of the form
     */
    protected function getFormToken(string $formName): string
    {
        $crawler = $this->client->getCrawler();
        $selectorPatterns = [
            'form[name="%s"] input[name="_token"]',
            '#%s input[name="_token"]',
            '#%s__token',
        ];

        foreach ($selectorPatterns as $pattern) {
            $selector = sprintf($pattern, $formName);
            $results = $crawler->filter($selector);

            if ($results->count() >= 1) {
                return $results->attr('value');
            }
        }

        throw new \Exception(sprintf('The form with id or name "%s" has not been found.', $formName));
    }

    protected function getJsonContent(): mixed
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    protected function clickLink(string $selector): void
    {
        $link = $this->client->getCrawler()->filter($selector)->link();

        $this->client->click($link);
    }
}
