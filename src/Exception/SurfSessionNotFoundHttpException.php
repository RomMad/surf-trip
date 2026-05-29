<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SurfSessionNotFoundHttpException extends NotFoundHttpException
{
    public function __construct(int $surfSessionId)
    {
        parent::__construct(sprintf('Surf session with id %d not found.', $surfSessionId));
    }
}
