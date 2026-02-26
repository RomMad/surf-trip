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
        ['John', 'Doe'],
        ['Alice', 'Martin'],
        ['Bob', 'Bernard'],
        ['Charlie', 'Petit'],
        ['Diana', 'Moreau'],
        ['Eve', 'Leroy'],
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
        foreach (self::USERS_DATA as [$firstname, $lastname]) {
            $user = new User();
            $email = sprintf('%s.%s@example.com', strtolower($firstname), strtolower($lastname));
            $password = $this->passwordHasher->hashPassword($user, 'test');

            $user
                ->setEmail($email)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setRoles(['ROLE_USER'])
                ->setPassword($password)
            ;

            yield $user;
        }
    }
}
