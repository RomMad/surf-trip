<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use App\Tests\Fixtures\DefaultStory;
use App\Tests\Fixtures\UserStory;
use App\Tests\Traits\ContainerTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminRouteGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

/**
 * @template Crud of CrudControllerInterface
 *
 * @extends AbstractCrudTestCase<Crud>
 */
#[ResetDatabase]
abstract class CustomAbstractCrudTestCase extends AbstractCrudTestCase
{
    use ContainerTrait;

    protected const string TABLE_SELECTOR = 'table.datagrid';
    protected const string FORM_BUTTON_SELECTOR = 'ea[newForm][btn]';

    protected function setUp(): void
    {
        parent::setUp();

        DefaultStory::load();

        $user = UserStory::getAdminUser();

        $this->client->loginUser($user);
        $this->client->followRedirects();

        $this->clearCache();

        $this->generateAdminRoutes();
    }

    /**
     * @return class-string<AdminDashboardController>
     */
    protected function getDashboardFqcn(): string
    {
        return AdminDashboardController::class;
    }

    /**
     * Fix: manually trigger route generation to populate the cache.
     */
    private function generateAdminRoutes(): void
    {
        $adminRouteGenerator = self::getContainer()->get(AdminRouteGenerator::class);

        if (!$adminRouteGenerator instanceof AdminRouteGenerator) {
            throw new \RuntimeException('AdminRouteGenerator service not found.');
        }

        if (null === $adminRouteGenerator->findRouteName(AdminDashboardController::class)) {
            $adminRouteGenerator->generateAll();
        }
    }
}
