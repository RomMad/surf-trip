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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const string USER_REFERENCE = 'user_';
    public const int USERS_COUNT = 80;
    private const string AVATARS_FIXTURE_DIRECTORY = 'fixtures/avatars';
    private const string AVATARS_PUBLIC_DIRECTORY = 'public/uploads/avatars';

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
        private readonly UserPasswordHasherInterface $passwordHasher,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $avatarFileNames = $this->getAvatarFileNames();
        $this->copyAvatarFixturesToPublicDirectory($avatarFileNames);

        $password = $this->passwordHasher->hashPassword(new User(), 'password');

        foreach ($this->generateUsers($avatarFileNames) as $index => $user) {
            $user->password = $password;

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE.$index, $user);
        }

        for ($index = count(self::USERS_DATA); $index < self::USERS_COUNT; ++$index) {
            $user = $this->generateRandomUser($avatarFileNames);
            $user->password = $password;

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE.$index, $user);
        }

        $manager->flush();
    }

    /**
     * @param list<string> $avatarFileNames
     *
     * @return \Generator<int, User>
     */
    private function generateUsers(array $avatarFileNames): \Generator
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
            $user->avatarPath = $this->faker->randomElement($avatarFileNames);

            yield $user;
        }
    }

    /**
     * @param list<string> $avatarFileNames
     */
    private function generateRandomUser(array $avatarFileNames): User
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
        $user->avatarPath = $this->faker->randomElement($avatarFileNames);

        return $user;
    }

    /**
     * @return list<string>
     */
    private function getAvatarFileNames(): array
    {
        $avatarsDirectory = sprintf('%s/%s', $this->projectDir, self::AVATARS_FIXTURE_DIRECTORY);

        if (!is_dir($avatarsDirectory)) {
            throw new \LogicException(sprintf('Avatar fixtures directory not found: %s.', $avatarsDirectory));
        }

        $avatarFileNames = array_values(array_filter(
            scandir($avatarsDirectory) ?: [],
            static fn (string $fileName): bool => is_file(sprintf('%s/%s', $avatarsDirectory, $fileName)),
        ));

        if ([] === $avatarFileNames) {
            throw new \LogicException(sprintf('No avatar fixture found in directory: %s.', $avatarsDirectory));
        }

        return $avatarFileNames;
    }

    /**
     * @param list<string> $avatarFileNames
     */
    private function copyAvatarFixturesToPublicDirectory(array $avatarFileNames): void
    {
        $sourceDirectory = sprintf('%s/%s', $this->projectDir, self::AVATARS_FIXTURE_DIRECTORY);
        $destinationDirectory = sprintf('%s/%s', $this->projectDir, self::AVATARS_PUBLIC_DIRECTORY);

        if (!is_dir($destinationDirectory) && !mkdir($destinationDirectory, 0775, true) && !is_dir($destinationDirectory)) {
            throw new \RuntimeException(sprintf('Unable to create avatars destination directory: %s.', $destinationDirectory));
        }

        foreach ($avatarFileNames as $avatarFileName) {
            $source = sprintf('%s/%s', $sourceDirectory, $avatarFileName);
            $destination = sprintf('%s/%s', $destinationDirectory, $avatarFileName);

            if (!copy($source, $destination)) {
                throw new \RuntimeException(sprintf('Unable to copy avatar fixture from %s to %s.', $source, $destination));
            }
        }
    }
}
