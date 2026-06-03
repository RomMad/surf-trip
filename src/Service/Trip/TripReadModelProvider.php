<?php

declare(strict_types=1);

namespace App\Service\Trip;

use App\Cache\Trip\TripCacheKeys;
use App\Exception\TripNotFoundHttpException;
use App\ReadModel\Trip\TripShowReadModel;
use App\Repository\TripRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class TripReadModelProvider
{
    private const string CACHE_TTL = 'PT24H';

    public function __construct(
        private TripRepository $tripRepository,
        private TagAwareCacheInterface $cache,
    ) {}

    public function getById(int $id): TripShowReadModel
    {
        $trip = $this->cache->get(
            TripCacheKeys::readModel($id),
            function (ItemInterface $item) use ($id): ?TripShowReadModel {
                $item->expiresAfter(new \DateInterval(self::CACHE_TTL));

                return $this->tripRepository->findShowReadModelById($id);
            },
        );

        if (null === $trip) {
            $this->cache->delete(TripCacheKeys::readModel($id));

            throw new TripNotFoundHttpException($id);
        }

        return $trip;
    }
}
