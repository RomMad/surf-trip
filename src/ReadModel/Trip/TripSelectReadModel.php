<?php

declare(strict_types=1);

namespace App\ReadModel\Trip;

final readonly class TripSelectReadModel
{
    public function __construct(
        public int $id,
        public string $title,
        public string $location,
    ) {}

    public function getLabel(): string
    {
        return sprintf('%s - %s', $this->title, $this->location);
    }
}
