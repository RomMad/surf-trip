<?php

declare(strict_types=1);

namespace App\ObjectMapper;

use App\Entity\SurfSession;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<SurfSession, SurfSessionWriteModel>
 */
final readonly class SurfSessionStartAtToTimeTransformer implements TransformCallableInterface
{
    /**
     * @param SurfSession                $source
     * @param SurfSessionWriteModel|null $target
     */
    public function __invoke(mixed $startAt, object $source, ?object $target): mixed
    {
        if (!$startAt instanceof \DateTimeImmutable) {
            return null;
        }

        return $startAt->format('H:i');
    }
}
