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
        foreach (self::USERS_DATA as [$firstName, $lastName]) {
            $user = new User();
            $email = sprintf('%s.%s@example.com', strtolower($firstName), strtolower($lastName));
            $password = $this->passwordHasher->hashPassword($user, 'password');

            $user
                ->setEmail($email)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setRoles(['ROLE_USER'])
                ->setPassword($password)
            ;

            yield $user;
        }
    }
}
