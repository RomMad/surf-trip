<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Tests\CustomKernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
#[CoversClass(User::class)]
#[Small]
final class UserTest extends CustomKernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUser(): void
    {
        $user = $this->createValidUser();

        $violations = $this->validator->validate($user);

        $this->assertCount(0, $violations);
    }

    public function testInvalidUser(): void
    {
        $user = new User();

        $violations = $this->validator->validate($user);

        $this->assertCount(2, $violations);
    }

    public function testInvalidUserWithInvalidEmail(): void
    {
        $user = $this->createValidUser();
        $user->email = 'invalid-email';

        $violations = $this->validator->validate($user);

        $this->assertCount(1, $violations);
    }

    public function testInvalidUserWithEmptyFirstName(): void
    {
        $user = $this->createValidUser();
        $user->firstName = '';

        $violations = $this->validator->validate($user);

        $this->assertCount(1, $violations);
    }

    private function createValidUser(): User
    {
        return UserFactory::new()->withoutPersisting()->create();
    }
}
