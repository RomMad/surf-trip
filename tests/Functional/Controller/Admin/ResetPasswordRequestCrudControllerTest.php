<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Controller\Admin\ResetPasswordRequestCrudController;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @extends CustomAbstractCrudTestCase<ResetPasswordRequestCrudController>
 */
#[Medium]
final class ResetPasswordRequestCrudControllerTest extends CustomAbstractCrudTestCase
{
    /**
     * @return class-string<ResetPasswordRequestCrudController>
     */
    protected function getControllerFqcn(): string
    {
        return ResetPasswordRequestCrudController::class;
    }

    public function testIndexPageIsSuccessful(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateIndexUrl());

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists(self::TABLE_SELECTOR);
    }
}
