<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class DeleteTripController extends AbstractController
{
    public function __construct(
        private readonly TripRepository $tripRepository,
    ) {}

    #[Route(
        path: '/trip/{id:trip}/delete',
        name: 'app.trip.delete',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_POST]
    )]
    #[IsCsrfTokenValid(new Expression('"delete" ~ args["trip"].getId()'))]
    public function __invoke(Trip $trip): Response
    {
        $this->tripRepository->remove($trip, true);

        return $this->redirectToRoute(IndexTripController::ROUTE, [], Response::HTTP_SEE_OTHER);
    }
}
