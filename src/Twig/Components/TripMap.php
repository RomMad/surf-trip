<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\ReadModel\Trip\TripShowReadModel;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class TripMap
{
    private const int DEFAULT_ZOOM = 6;

    public TripShowReadModel $trip;

    public function getMap(): Map
    {
        $point = new Point(
            $this->trip->location->latitude,
            $this->trip->location->longitude,
        );

        return new Map()
            ->center($point)
            ->zoom(self::DEFAULT_ZOOM)
            ->addMarker(new Marker(
                position: $point,
                title: $this->trip->title->value,
                infoWindow: new InfoWindow(
                    content: sprintf(
                        '<strong>%s</strong><br>%s',
                        $this->trip->title->value,
                        $this->trip->location->label,
                    ),
                ),
            ))
        ;
    }
}
