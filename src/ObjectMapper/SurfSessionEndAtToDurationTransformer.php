<?php

declare(strict_types=1);

namespace App\ObjectMapper;

use App\Entity\SurfSession;
use App\Enum\SurfSession\SurfSessionDuration;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<SurfSession, SurfSessionWriteModel>
 */
final readonly class SurfSessionEndAtToDurationTransformer implements TransformCallableInterface
{
    /**
     * @param SurfSession                $source
     * @param SurfSessionWriteModel|null $target
     */
    public function __invoke(mixed $endAt, object $source, ?object $target): mixed
    {
        if (!$endAt instanceof \DateTimeImmutable || null === $source->startAt) {
            return null;
        }

        $durationInSeconds = $endAt->getTimestamp() - $source->startAt->getTimestamp();
        $minutes = max(1, (int) floor($durationInSeconds / 60));

        return SurfSessionDuration::fromMinutes($minutes);
    }
}
