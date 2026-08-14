<?php

declare(strict_types=1);

namespace App\ObjectMapper\Trip;

use App\Entity\Trip;
use App\Form\Model\Trip\TripWriteModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<Trip, TripWriteModel>
 */
final readonly class PublishedAtToIsPublishedTransformer implements TransformCallableInterface
{
    /**
     * @param \DateTimeImmutable|null $publishedAt
     * @param Trip                    $source
     * @param TripWriteModel          $target
     *
     * @return bool
     */
    public function __invoke(mixed $publishedAt, object $source, ?object $target): mixed
    {
        return null !== $publishedAt;
    }
}
