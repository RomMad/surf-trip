<?php

declare(strict_types=1);

namespace App\Service\Map;

class GoogleMaps
{
    private const string SEARCH_URL = 'https://www.google.com/maps/search/?api=1&query=';

    public static function generateSearchUrl(?string $query = null): ?string
    {
        if (null === $query) {
            return null;
        }

        return self::SEARCH_URL.urlencode($query);
    }
}
