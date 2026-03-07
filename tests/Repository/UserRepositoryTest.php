<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Tests\CustomKernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;

/**
 * @internal
 */
#[CoversClass(UserRepository::class)]
#[Medium]
final class UserRepositoryTest extends CustomKernelTestCase
{
    private ?UserRepository $repository = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getContainer()->get(UserRepository::class);
    }

    public function testUpgradePassword(): void
    {
        $user = UserFactory::createOne();

        $this->repository->upgradePassword($user, 'newhash');

        $this->assertSame('newhash', $user->password);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->repository = null;
    }
}
