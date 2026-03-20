<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Exception\TripNotFoundHttpException;
use App\Service\Trip\TripReadModelProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class ShowTripController extends AbstractController
{
    public const string ROUTE = 'app.trip.show';

    public function __construct(
        private readonly TripReadModelProvider $tripReadModelProvider,
    ) {}

    #[Route(
        path: '/trip/{id}/{slug}',
        name: self::ROUTE,
        requirements: [
            'id' => Requirement::POSITIVE_INT,
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: [Request::METHOD_GET]
    )]
    public function __invoke(int $id, string $slug): Response
    {
        $trip = $this->tripReadModelProvider->getById($id);

        if (null === $trip) {
            throw new TripNotFoundHttpException($id);
        }

        if ($trip->slug->value !== $slug) {
            return $this->redirectToRoute(self::ROUTE, [
                'id' => $trip->id,
                'slug' => $trip->slug->value,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }
}
