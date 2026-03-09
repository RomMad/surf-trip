<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\FirstName;
use App\Tests\CustomKernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\InvalidArgumentException;

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

    public function testInvalidUserWithInvalidEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('user.email.invalid');

        $user = new User();
        $user->email = (new Email('invalid-email'));
    }

    public function testInvalidUserWithEmptyFirstName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('user.first_name.not_blank');

        $user = new User();
        $user->firstName = new FirstName('');
    }
}
