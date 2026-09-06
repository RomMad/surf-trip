<?php

declare(strict_types=1);

namespace App\ReadModel\Trip;

use App\Calendar\CalendarLinkableInterface;
use App\Entity\User;
use App\Entity\ValueObject\Slug;
use App\Entity\ValueObject\Title;
use App\Enum\Trip\TripStatus;
use App\Enum\User\SurfLevel;
use App\ReadModel\LocationReadModel;

abstract readonly class AbstractTripReadModel implements TripOwnershipAwareInterface, CalendarLinkableInterface
{
    use TripCalendarLinkableTrait;

    /** @var array<TripOwnerReadModel> */
    public array $owners;

    /**
     * @param SurfLevel[] $requiredLevels
     */
    public function __construct(
        public int $id,
        public Slug $slug,
        public Title $title,
        public LocationReadModel $location,
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt,
        public array $requiredLevels,
        public ?string $description,
        public string $creatorName,
        public ?\DateTimeImmutable $publishedAt,
        string $ownersJson,
    ) {
        $decoded = json_decode($ownersJson, true);

        $this->owners = array_map(
            fn (array $owner): TripOwnerReadModel => new TripOwnerReadModel(...$owner),
            is_array($decoded) ? $decoded : [],
        );
    }

    public function isOwnedByUser(User $user): bool
    {
        return array_any($this->owners, static fn (TripOwnerReadModel $owner): bool => $owner->id === $user->id);
    }

    public function getStatus(): TripStatus
    {
        if (!$this->isPublished()) {
            return TripStatus::Draft;
        }

        return TripStatus::fromPeriod($this->startAt, $this->endAt);
    }

    public function isPublished(): bool
    {
        return null !== $this->publishedAt;
    }
}
