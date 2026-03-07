<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    public const string JOHN_EMAIL = 'john.doe@test.com';
    public const string JOHN_PASSWORD = UserFactory::DEFAULT_PASSWORD;

    public function build(): void
    {
        UserFactory::createOne([
            'email' => self::JOHN_EMAIL,
            'firstName' => 'John',
            'lastName' => 'Doe',
        ]);

        UserFactory::createMany(5);
    }

    public static function getJohnUser(): User
    {
        return UserFactory::find(['email' => self::JOHN_EMAIL]);
    }
}
