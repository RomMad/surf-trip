<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Controller\User\DashboardController;
use App\Entity\ValueObject\Email;
use App\Factory\UserFactory;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\DashboardStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(DashboardController::class)]
#[Medium]
final class DashboardControllerTest extends CustomWebTestCase
{
    private const string PATH = '/en/me/dashboard';
    private const string DASHBOARD_TITLE = 'Dashboard';
    private const string MAIN_SELECTOR = 'main';
    private const string CANVAS_SELECTOR = 'canvas';
    private const string KPI_TITLE_SELECTOR = '.app-kpi-title';
    private const string KPI_VALUE_SELECTOR = '.app-kpi-value';
    private const int KPI_COUNT = 5;

    protected function setUp(): void
    {
        $this->setUpTest(DashboardStory::class);
    }

    public function testDashboardPageDisplaysAggregatedStatistics(): void
    {
        $user = DashboardStory::getDashboardUser();

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::TITLE_H1, self::DASHBOARD_TITLE);
        $this->assertSelectorExists(self::CANVAS_SELECTOR);

        $crawler = $this->client->getCrawler();
        $kpiTitles = $crawler->filter(self::KPI_TITLE_SELECTOR);
        $kpiValues = $crawler->filter(self::KPI_VALUE_SELECTOR);

        $this->assertCount(self::KPI_COUNT, $kpiTitles);
        $this->assertCount(self::KPI_COUNT, $kpiValues);

        $this->assertStringContainsString('Total trips', $kpiTitles->eq(0)->text());
        $this->assertStringContainsString('2', $kpiValues->eq(0)->text());
        $this->assertStringContainsString('Trips this year', $kpiTitles->eq(1)->text());
        $this->assertStringContainsString('1', $kpiValues->eq(1)->text());
        $this->assertStringContainsString('Total sessions', $kpiTitles->eq(2)->text());
        $this->assertStringContainsString('4', $kpiValues->eq(2)->text());
        $this->assertStringContainsString('Sessions this year', $kpiTitles->eq(3)->text());
        $this->assertStringContainsString('3', $kpiValues->eq(3)->text());
        $this->assertStringContainsString('Average session rating', $kpiTitles->eq(4)->text());
        $this->assertStringContainsString('3.7', $kpiValues->eq(4)->text());

        $this->assertSelectorTextContains(self::MAIN_SELECTOR, 'Top spots');
        $this->assertSelectorTextContains(self::MAIN_SELECTOR, 'Number of sessions per spot with the average rating shown in the label.');
        $this->assertSelectorTextContains(self::MAIN_SELECTOR, 'Sessions and trips by year');
        $this->assertSelectorExists(self::CANVAS_SELECTOR);
    }

    public function testDashboardPageShowsEmptyStatesForAUserWithoutData(): void
    {
        $user = UserFactory::find(['email' => Email::from(DashboardStory::USER_WITHOUT_ACTIVITY_EMAIL)]);

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, self::PATH);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::TITLE_H1, self::DASHBOARD_TITLE);
        $this->assertSelectorNotExists(self::CANVAS_SELECTOR);
        $this->assertSelectorTextContains(self::MAIN_SELECTOR, 'No dashboard data yet');
        $this->assertSelectorTextContains(self::MAIN_SELECTOR, 'No session data yet.');
        $this->assertSelectorTextContains(self::MAIN_SELECTOR, 'No spot data yet.');
        $this->assertSelectorTextContains(self::MAIN_SELECTOR, 'No yearly activity data yet.');
    }
}
