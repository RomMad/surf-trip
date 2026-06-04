<?php

declare(strict_types=1);

namespace App\ObjectMapper;

use App\Entity\SurfSession;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<SurfSessionWriteModel, SurfSession>
 */
final readonly class SurfSessionWriteModelToStartAtTransformer implements TransformCallableInterface
{
    /**
     * @param SurfSessionWriteModel $source
     * @param SurfSession|null      $target
     */
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        return $source->getStartAt();
    }
}
