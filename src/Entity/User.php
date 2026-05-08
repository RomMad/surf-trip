<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Doctrine\Type\EmailType;
use App\Doctrine\Type\FirstNameType;
use App\Doctrine\Type\LastNameType;
use App\Doctrine\Type\UsernameType;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\FirstName;
use App\Entity\ValueObject\LastName;
use App\Entity\ValueObject\Username;
use App\Enum\User\Locale;
use App\Enum\User\SurfLevel;
use App\Enum\User\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['email'], message: 'user.email.unique')]
#[UniqueEntity(fields: ['username'], message: 'user.username.unique')]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']]
)]
final class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    public private(set) ?int $id = null;

    #[ORM\Column(type: EmailType::NAME)]
    public Email $email;

    #[ORM\Column(type: UsernameType::NAME)]
    #[ApiProperty(identifier: true)]
    #[Groups(['user:read', 'trip:read'])]
    public Username $username;

    #[ORM\Column(type: FirstNameType::NAME)]
    #[Groups(['user:read', 'trip:read'])]
    public FirstName $firstName;

    #[ORM\Column(type: LastNameType::NAME, nullable: true)]
    #[Groups(['user:read', 'trip:read'])]
    public ?LastName $lastName = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    public ?string $avatar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user:read'])]
    public ?string $description = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, enumType: SurfLevel::class)]
    #[Groups(['user:read'])]
    public ?SurfLevel $level = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    public ?string $location = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['user:read'])]
    public ?string $instagram = null;

    /** @var list<string> The user roles */
    #[ORM\Column]
    #[Groups(['user:read'])]
    public array $roles = [] {
        get {
            $roles = $this->roles;
            $roles[] = UserRole::USER;

            return array_unique($roles);
        }
    }
    /** @var string The hashed password */
    #[ORM\Column]
    public string $password = '';

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\ManyToMany(targetEntity: Trip::class, mappedBy: 'owners')]
    public Collection $trips;

    #[ORM\Column]
    public bool $isVerified = false;

    #[ORM\Column(length: 2, enumType: Locale::class)]
    public Locale $locale = Locale::DEFAULT;

    #[ORM\Column]
    public \DateTimeImmutable $createdAt;

    #[ORM\Column]
    public \DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $lastActiveAt = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->trips = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->email;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', (string) $this->password);

        return $data;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', (string) $this->firstName, (string) $this->lastName);
    }

    /** Keep this method for compatibility with Symfony's security and PasswordAuthenticatedUserInterface */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = UserRole::USER;

        return array_unique($roles);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function hasRole(UserRole $role): bool
    {
        return in_array($role->value, $this->roles, true);
    }

    public function addTrip(Trip $trip): static
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->addOwner($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): static
    {
        if ($this->trips->removeElement($trip)) {
            $trip->removeOwner($this);
        }

        return $this;
    }
}
