<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\FirstName;
use App\Entity\ValueObject\LastName;
use App\Entity\ValueObject\Username;
use App\Enum\User\SurfLevel;
use App\Enum\User\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const string USER_REFERENCE = 'user_';
    public const int USERS_COUNT = 80;

    private const array USERS_DATA = [
        ['John', 'Doe', UserRole::Admin],
        ['Alice', 'Martin', UserRole::User],
        ['Bob', 'Bernard', UserRole::User],
        ['Charlie', 'Petit', UserRole::User],
        ['Diana', 'Moreau', UserRole::User],
        ['Eve', 'Leroy', UserRole::User],
    ];

    private readonly Generator $faker;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $password = $this->passwordHasher->hashPassword(new User(), 'password');

        foreach ($this->generateUsers() as $index => $user) {
            $user->password = $password;

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE.$index, $user);
        }

        for ($index = count(self::USERS_DATA); $index < self::USERS_COUNT; ++$index) {
            $user = $this->generateRandomUser();
            $user->password = $password;

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
            $user->email = Email::from(sprintf('%s.%s@test.com', strtolower($firstName), strtolower($lastName)));
            $user->username = Username::from(strtolower(sprintf('%s.%s', $firstName, $lastName)));
            $user->firstName = FirstName::from($firstName);
            $user->lastName = LastName::from($lastName);
            $user->roles = [$role->value];
            $user->isVerified = true;
            $user->level = $this->faker->randomElement(SurfLevel::cases());
            $user->location = $this->faker->city();
            $user->description = $this->faker->paragraph();

            yield $user;
        }
    }

    private function generateRandomUser(): User
    {
        $user = new User();
        $user->email = Email::from($this->faker->unique()->safeEmail());
        $user->username = Username::from($this->faker->unique()->userName());
        $user->firstName = FirstName::from($this->faker->firstName());
        $user->lastName = LastName::from($this->faker->lastName());
        $user->roles = [UserRole::User->value];
        $user->isVerified = true;
        $user->level = $this->faker->randomElement(SurfLevel::cases());
        $user->location = $this->faker->city();
        $user->description = $this->faker->paragraph();

        return $user;
    }
}
