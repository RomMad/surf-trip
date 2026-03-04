<?php

declare(strict_types=1);

namespace App\ReadModel\Trip;

use App\Entity\User;
use App\Entity\ValueObject\Location;
use App\Entity\ValueObject\Slug;
use App\Entity\ValueObject\Title;
use App\Enum\Trip\RequiredLevel;

abstract readonly class AbstractTripReadModel implements TripOwnershipAwareInterface
{
    /** @var list<TripOwnerReadModel> */
    public array $owners;

    /**
     * @param RequiredLevel[] $requiredLevels
     */
    public function __construct(
        public int $id,
        public Slug $slug,
        public Title $title,
        public Location $location,
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt,
        public array $requiredLevels,
        public ?string $description,
        public \DateTimeImmutable $createdAt,
        string $ownersJson,
    ) {
        $this->owners = array_map(
            fn (array $owner): TripOwnerReadModel => new TripOwnerReadModel(...$owner),
            json_decode($ownersJson),
        );
    }

    public function isOwnedByUser(User $user): bool
    {
        return array_any($this->owners, static fn (TripOwnerReadModel $owner): bool => $owner->id === $user->getId());
    }

    public function getOwnerNames(): string
    {
        $ownerNames = array_map(static fn (TripOwnerReadModel $owner): string => $owner->fullName, $this->owners);

        return implode(', ', $ownerNames);
    }
}
