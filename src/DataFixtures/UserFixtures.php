<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\User\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user_';

    private const array USERS_DATA = [
        ['John', 'Doe', UserRole::Admin],
        ['Alice', 'Martin', UserRole::User],
        ['Bob', 'Bernard', UserRole::User],
        ['Charlie', 'Petit', UserRole::User],
        ['Diana', 'Moreau', UserRole::User],
        ['Eve', 'Leroy', UserRole::User],
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        foreach ($this->generateUsers() as $index => $user) {
            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE.$index, $user);
        }

        $manager->flush();
    }

    /**
     * @return \Generator<int, User>
     */
    private function generateUsers(): \Generator
    {
        foreach (self::USERS_DATA as [$firstName, $lastName, $role]) {
            $user = new User();
            $user->email = sprintf('%s.%s@example.com', strtolower($firstName), strtolower($lastName));
            $user->firstName = $firstName;
            $user->lastName = $lastName;
            $user->password = $this->passwordHasher->hashPassword($user, 'password');
            $user->roles = [$role->value];
            $user->isVerified = true;

            yield $user;
        }
    }
}
