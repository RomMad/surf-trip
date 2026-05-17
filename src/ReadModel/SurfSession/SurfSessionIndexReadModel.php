<?php

declare(strict_types=1);

namespace App\ReadModel\SurfSession;

use App\Enum\SurfSession\SurfSessionRating;

final readonly class SurfSessionIndexReadModel
{
    public function __construct(
        public int $id,
        public string $spot,
        public ?string $board,
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt,
        public ?SurfSessionRating $rating,
        public ?string $objective,
        public ?string $comment,
    ) {}
}
