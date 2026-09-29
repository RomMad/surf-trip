<?php

declare(strict_types=1);

namespace App\State\Trip;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Cache\Trip\TripCacheInvalidator;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @implements ProcessorInterface<Trip, Trip>
 */
final readonly class UnpublishTripProcessor implements ProcessorInterface
{
    public function __construct(
        private TripRepository $tripRepository,
        private TripCacheInvalidator $tripCacheInvalidator,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Trip
    {
        if (!$data instanceof Trip) {
            throw new BadRequestHttpException('Invalid data provided');
        }

        $data->unpublish();

        $this->tripRepository->save($data, true);
        $this->tripCacheInvalidator->invalidate($data);

        return $data;
    }
}
