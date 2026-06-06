<?php

declare(strict_types=1);

namespace App\DataFixtures\Data;

class SurfTripData
{
    /**
     * @var array<string, array{location: string, country: string, spots: array<int, string>}>
     */
    public const array ALL = [
        'Bali' => [
            'location' => 'Bali',
            'country' => 'Indonesia',
            'spots' => [
                'Canggu',
                'Uluwatu',
                'Padang Padang',
                'Bingin',
            ],
        ],
        'Nazaré' => [
            'location' => 'Nazaré',
            'country' => 'Portugal',
            'spots' => [
                'Praia do Norte',
                'São Martinho do Porto',
                'Praia da Nazaré',
            ],
        ],
        'Honolulu' => [
            'location' => 'Honolulu',
            'country' => 'USA',
            'spots' => [
                'Waikiki',
                'Ala Moana Bowls',
                'Diamond Head',
            ],
        ],
        'North Shore Oahu' => [
            'location' => 'North Shore Oahu',
            'country' => 'USA',
            'spots' => [
                'Pipeline',
                'Sunset Beach',
                'Haleiwa',
            ],
        ],
        'Guanacaste' => [
            'location' => 'Guanacaste',
            'country' => 'Costa Rica',
            'spots' => [
                'Tamarindo',
                "Witch's Rock",
                'Playa Avellanas',
            ],
        ],
        'Fuerteventura' => [
            'location' => 'Fuerteventura',
            'country' => 'Spain',
            'spots' => [
                'El Cotillo',
                'Punta Blanca',
                'Rocky Point',
            ],
        ],
        "Teahupo'o" => [
            'location' => "Teahupo'o",
            'country' => 'French Polynesia',
            'spots' => [
                "Teahupo'o",
                'Papara',
                'Taapuna',
            ],
        ],
        'Byron Bay' => [
            'location' => 'Byron Bay',
            'country' => 'Australia',
            'spots' => [
                'The Pass',
                'Wategos',
                'Lennox Head',
            ],
        ],
        'Essaouira' => [
            'location' => 'Essaouira',
            'country' => 'Morocco',
            'spots' => [
                'Essaouira',
                'Sidi Kaouki',
                'Imsouane',
            ],
        ],
        'Hossegor' => [
            'location' => 'Hossegor',
            'country' => 'France',
            'spots' => [
                'La Gravière',
                'Les Culs Nus',
                'Les Bourdaines',
            ],
        ],
        'Biarritz' => [
            'location' => 'Biarritz',
            'country' => 'France',
            'spots' => [
                'Côte des Basques',
                'Grande Plage',
                'Anglet',
            ],
        ],
        'Lacanau' => [
            'location' => 'Lacanau',
            'country' => 'France',
            'spots' => [
                'Lacanau Océan',
                'Lacanau Sud',
                'Carcans Plage',
            ],
        ],
        'La Torche' => [
            'location' => 'La Torche',
            'country' => 'France',
            'spots' => [
                'La Torche',
                'Pors Carn',
                'Baie des Trépassés',
            ],
        ],
        'Mundaka' => [
            'location' => 'Mundaka',
            'country' => 'Spain',
            'spots' => [
                'Mundaka',
                'Sopelana',
                'Zarautz',
            ],
        ],
        'Jeffreys Bay' => [
            'location' => 'Jeffreys Bay',
            'country' => 'South Africa',
            'spots' => [
                'Supertubes',
                'Point',
                'Cape St Francis',
            ],
        ],
        'Peniche' => [
            'location' => 'Peniche',
            'country' => 'Portugal',
            'spots' => [
                'Supertubos',
                'Baleal',
                'Praia do Medão',
            ],
        ],
    ];
}
