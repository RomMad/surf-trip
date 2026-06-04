<?php

declare(strict_types=1);

namespace App\ObjectMapper;

use App\Entity\SurfSession;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<SurfSessionWriteModel, SurfSession>
 */
final readonly class SurfSessionWriteModelToEndAtTransformer implements TransformCallableInterface
{
    /**
     * @param SurfSessionWriteModel $source
     * @param SurfSession|null      $target
     */
    public function __invoke(mixed $duration, object $source, ?object $target): mixed
    {
        $startAt = $source->getStartAt();

        if (null === $startAt || null === $source->durationMinutes) {
            return null;
        }

        return $startAt->modify(sprintf('+%d minutes', $source->durationMinutes->value));
    }
}
