<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class ShowTripController extends AbstractController
{
    public function __construct(
        private readonly TripRepository $tripRepository,
    ) {}

    #[Route(
        path: '/trip/{id}',
        name: 'app.trip.show',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_GET]
    )]
    public function __invoke(int $id): Response
    {
        $trip = $this->tripRepository->findShowReadModelById($id);

        if (null === $trip) {
            throw $this->createNotFoundException();
        }

        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }
}
