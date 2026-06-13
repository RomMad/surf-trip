<?php

declare(strict_types=1);

namespace App\DataFixtures\Data;

class SurfTripData
{
    /**
     * @var array<string, array{
     *  location: array{label: string, latitude: float, longitude: float, placeId: string},
     *  country: string,
     *  spots: list<string>
     * }>
     */
    public const array ALL = [
        'Bali' => [
            'location' => [
                'label' => 'Bali',
                'latitude' => -8.652497,
                'longitude' => 115.219118,
                'placeId' => 'region.91239',
            ],
            'country' => 'Indonesia',
            'spots' => [
                'Canggu',
                'Uluwatu',
                'Padang Padang',
                'Bingin',
            ],
        ],
        'Nazaré' => [
            'location' => [
                'label' => 'Nazaré',
                'latitude' => 39.60146,
                'longitude' => -9.071826,
                'placeId' => 'place.17991866',
            ],

            'country' => 'Portugal',
            'spots' => [
                'Praia do Norte',
                'São Martinho do Porto',
                'Praia da Nazaré',
            ],
        ],
        'Honolulu' => [
            'location' => [
                'label' => 'Honolulu',
                'latitude' => 21.308499,
                'longitude' => -157.861535,
                'placeId' => 'place.153061612I',
            ],
            'country' => 'USA',
            'spots' => [
                'Waikiki',
                'Ala Moana Bowls',
                'Diamond Head',
            ],
        ],
        'North Shore Oahu' => [
            'location' => [
                'label' => 'North Shore Oahu',
                'latitude' => 21.5933,
                'longitude' => -158.1108,
                'placeId' => 'ChIJiQyXoZpHhS4R8g7QyXoZpI',
            ],
            'country' => 'USA',
            'spots' => [
                'Pipeline',
                'Sunset Beach',
                'Haleiwa',
            ],
        ],
        'Guanacaste' => [
            'location' => [
                'label' => 'Guanacaste',
                'latitude' => 10.631979,
                'longitude' => -85.439317,
                'placeId' => 'region.58419',
            ],
            'country' => 'Costa Rica',
            'spots' => [
                'Tamarindo',
                "Witch's Rock",
                'Playa Avellanas',
            ],
        ],
        'Fuerteventura' => [
            'location' => [
                'label' => 'Fuerteventura',
                'latitude' => 28.3587,
                'longitude' => -14.0531,
                'placeId' => 'place.67250246',
            ],
            'country' => 'Spain',
            'spots' => [
                'El Cotillo',
                'Punta Blanca',
                'Rocky Point',
            ],
        ],
        "Teahupo'o" => [
            'location' => [
                'label' => "Teahupo'o",
                'latitude' => -17.847389,
                'longitude' => -149.268205,
                'placeId' => 'place.575665',
            ],
            'country' => 'French Polynesia',
            'spots' => [
                "Teahupo'o",
                'Papara',
                'Taapuna',
            ],
        ],
        'Byron Bay' => [
            'location' => [
                'label' => 'Byron Bay',
                'latitude' => -28.642212,
                'longitude' => 153.61235,
                'placeId' => 'place.4597774',
            ],
            'country' => 'Australia',
            'spots' => [
                'The Pass',
                'Wategos',
                'Lennox Head',
            ],
        ],
        'Essaouira' => [
            'location' => [
                'label' => 'Essaouira',
                'latitude' => 31.513723,
                'longitude' => -9.771017,
                'placeId' => 'place.4139147',
            ],
            'country' => 'Morocco',
            'spots' => [
                'Essaouira',
                'Sidi Kaouki',
                'Imsouane',
            ],
        ],
        'Hossegor' => [
            'location' => [
                'label' => 'Hossegor',
                'latitude' => 43.659363,
                'longitude' => -1.428128,
                'placeId' => 'place.253020237',
            ],
            'country' => 'France',
            'spots' => [
                'La Gravière',
                'Les Culs Nus',
                'Les Bourdaines',
            ],
        ],
        'Biarritz' => [
            'location' => [
                'label' => 'Biarritz',
                'latitude' => 43.4821,
                'longitude' => -1.559175,
                'placeId' => 'place.27011149',
            ],
            'country' => 'France',
            'spots' => [
                'Côte des Basques',
                'Grande Plage',
                'Anglet',
            ],
        ],
        'Lacanau' => [
            'location' => [
                'label' => 'Lacanau',
                'latitude' => 44.978123,
                'longitude' => -1.079243,
                'placeId' => 'place.115910733',
            ],
            'country' => 'France',
            'spots' => [
                'Lacanau Océan',
                'Lacanau Sud',
                'Carcans Plage',
            ],
        ],
        'Plomeur' => [
            'location' => [
                'label' => 'Plomeur',
                'comment' => 'La Torche',
                'latitude' => 47.8730,
                'longitude' => -4.2950,
                'placeId' => 'place.191760461',
            ],
            'country' => 'France',
            'spots' => [
                'La Torche',
                'Pors Carn',
                'Baie des Trépassés',
            ],
        ],
        'Mundaka' => [
            'location' => [
                'label' => 'Mundaka',
                'latitude' => 43.4076,
                'longitude' => -2.698501,
                'placeId' => 'place.39151686',
            ],
            'country' => 'Spain',
            'spots' => [
                'Mundaka',
                'Sopelana',
                'Zarautz',
            ],
        ],
        'Jeffreys Bay' => [
            'location' => [
                'label' => 'Jeffreys Bay',
                'latitude' => -34.052315,
                'longitude' => 24.92266,
                'placeId' => 'place.31639806',
            ],
            'country' => 'South Africa',
            'spots' => [
                'Supertubes',
                'Point',
                'Cape St Francis',
            ],
        ],
        'Peniche' => [
            'location' => [
                'label' => 'Peniche',
                'latitude' => 39.35564,
                'longitude' => -9.37856,
                'placeId' => 'place.20433082',
            ],
            'country' => 'Portugal',
            'spots' => [
                'Supertubos',
                'Baleal',
                'Praia do Medão',
            ],
        ],
    ];
}
