<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\FirstName;
use App\Entity\ValueObject\LastName;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    public const string JOHN_EMAIL = 'john.doe@test.com';
    public const string JOHN_PASSWORD = UserFactory::DEFAULT_PASSWORD;

    public function build(): void
    {
        UserFactory::createOne([
            'email' => Email::from(self::JOHN_EMAIL),
            'firstName' => FirstName::from('John'),
            'lastName' => LastName::from('Doe'),
        ]);

        UserFactory::createMany(5);
    }

    public static function getJohnUser(): User
    {
        return UserFactory::find(['email' => Email::from(self::JOHN_EMAIL)]);
    }
}
