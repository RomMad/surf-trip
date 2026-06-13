<?php

declare(strict_types=1);

namespace App\Twig\Function;

use App\Service\Map\GoogleMaps;
use Twig\Attribute\AsTwigFunction;

final class GoogleMapsUrl
{
    #[AsTwigFunction('google_maps_url')]
    public static function generateSearchUrl(?string $query = null): string
    {
        return GoogleMaps::generateSearchUrl($query);
    }
}
