<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user_';

    private const array USERS_DATA = [
        ['email' => 'alice@example.com', 'password' => 'password123'],
        ['email' => 'bob@example.com', 'password' => 'password123'],
        ['email' => 'charlie@example.com', 'password' => 'password123'],
        ['email' => 'diana@example.com', 'password' => 'password123'],
        ['email' => 'eve@example.com', 'password' => 'password123'],
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
        foreach (self::USERS_DATA as $userData) {
            $user = (new User());
            $user
                ->setEmail($userData['email'])
                ->setRoles(['ROLE_USER'])
                ->setPassword(
                    $this->passwordHasher->hashPassword($user, $userData['password'])
                )
            ;

            yield $user;
        }
    }
}
