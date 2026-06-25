<?php

declare(strict_types=1);

namespace App\ReadModel\Trip;

use App\Enum\User\SurfLevel;
use App\ReadModel\LocationReadModel;

final readonly class MapTripReadModel
{
    /**
     * @param SurfLevel[] $requiredLevels
     */
    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public LocationReadModel $location,
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt,
        public array $requiredLevels,
        public ?string $description,
    ) {}
}
