<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Trip;
use App\Entity\ValueObject\Slug;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Trip::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Trip::class)]
final readonly class TripSlugListener
{
    public function __construct(
        private SluggerInterface $slugger
    ) {}

    public function __invoke(Trip $trip): void
    {
        $slugValue = $this->slugger
            ->slug($trip->getTitle())
            ->toString()
         |> strtolower(...)
        ;
        $slug = Slug::tryFrom($slugValue);

        $trip->setSlug($slug);
    }
}
