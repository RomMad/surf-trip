<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\FirstName;
use App\Entity\ValueObject\LastName;
use App\Enum\User\UserRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public const string DEFAULT_PASSWORD = 'password';

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(
        private readonly ?UserPasswordHasherInterface $passwordHasher = null
    ) {
        parent::__construct();
    }

    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'email' => Email::from(self::faker()->unique()->safeEmail()),
            'firstName' => FirstName::from(self::faker()->firstName()),
            'lastName' => LastName::from(self::faker()->lastName()),
            'isVerified' => true,
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRole::USER],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                if (null !== $this->passwordHasher) {
                    $user->password = $this->passwordHasher->hashPassword($user, $user->getPassword());
                }
            })
        ;
    }
}
