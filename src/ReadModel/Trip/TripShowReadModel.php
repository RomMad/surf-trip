<?php

declare(strict_types=1);

namespace App\ReadModel\Trip;

use App\Enum\RequiredLevel;

final readonly class TripShowReadModel
{
    /**
     * @param RequiredLevel[] $requiredLevels
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $location,
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt,
        public array $requiredLevels,
        public ?string $description,
        public \DateTimeImmutable $createdAt,
        public string $ownerNames,
    ) {}
}
