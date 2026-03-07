<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Symfony\Component\DomCrawler\Crawler;

trait AssertsWebTestTrait
{
    protected const string ALERT_SUCCESS = '.app-alert.app-alert-success';
    protected const string ALERT_WARNING = '.app-alert.app-alert-warning';
    protected const string ALERT_DANGER = '.app-alert.app-alert-danger';

    public function assertAlertSuccessExists(): void
    {
        $this->assertSelectorExists(self::ALERT_SUCCESS);
    }

    /**
     * Asserts that the links of the sortable table give a successful response.
     */
    public function assertSortableTableLinksAreValid(): void
    {
        $this->client->getCrawler()->filter('table th>a')->each(function (Crawler $node): void {
            $this->client->click($node->link());
            $this->assertResponseIsSuccessful(sprintf('The link "%s" is not valid.', $node->text()), verbose: false);
        });
    }
}
