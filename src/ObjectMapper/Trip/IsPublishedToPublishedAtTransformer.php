<?php

declare(strict_types=1);

namespace App\ObjectMapper\Trip;

use App\Entity\Trip;
use App\Form\Model\Trip\TripWriteModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<TripWriteModel, Trip>
 */
final readonly class IsPublishedToPublishedAtTransformer implements TransformCallableInterface
{
    /**
     * @param bool           $isPublished
     * @param TripWriteModel $source
     * @param Trip           $target
     *
     * @return \DateTimeImmutable|null
     */
    public function __invoke(mixed $isPublished, object $source, ?object $target): mixed
    {
        $target?->togglePublished($isPublished);

        return $target?->publishedAt;
    }
}
