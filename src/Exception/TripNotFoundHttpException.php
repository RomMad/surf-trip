<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TripNotFoundHttpException extends NotFoundHttpException
{
    public function __construct(int $tripId)
    {
        parent::__construct(sprintf('Trip with id %d not found.', $tripId));
    }
}
