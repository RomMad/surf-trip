<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\ReadModel\Trip\MapTripReadModel;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Twig\Environment;

#[AsTwigComponent]
final class TripsMap
{
    private const int DEFAULT_ZOOM = 2;
    private const float PARIS_LATITUDE = 48.8566;
    private const float PARIS_LONGITUDE = 2.3522;

    /**
     * @var MapTripReadModel[]
     */
    public array $trips;

    public function __construct(
        private readonly Environment $twig,
    ) {}

    public function getMap(): Map
    {
        $map = new Map()
            ->center(new Point(
                self::PARIS_LATITUDE,
                self::PARIS_LONGITUDE,
            ))
            ->zoom(self::DEFAULT_ZOOM)
            ->minZoom(self::DEFAULT_ZOOM)
        ;

        foreach ($this->trips as $trip) {
            $map->addMarker(new Marker(
                position: new Point(
                    $trip->location->latitude,
                    $trip->location->longitude,
                ),
                title: $trip->title,
                infoWindow: new InfoWindow(
                    content: $this->twig->render('components/Trip/Map/Marker/InfoWindow.html.twig', [
                        'trip' => $trip,
                    ]),
                ),
            ));
        }

        return $map;
    }
}
