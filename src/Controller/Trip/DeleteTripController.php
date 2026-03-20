<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Repository\TripRepository;
use App\Security\Voter\TripVoter;
use App\Service\Trip\TripReadModelProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteTripController extends AbstractController
{
    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly TripReadModelProvider $tripReadModelProvider,
    ) {}

    #[Route(
        path: '/trip/{id:trip}/delete',
        name: 'app.trip.delete',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_POST]
    )]
    #[IsCsrfTokenValid(new Expression('"delete" ~ args["trip"].id'))]
    #[IsGranted(TripVoter::DELETE, subject: 'trip')]
    public function __invoke(Trip $trip): Response
    {
        $tripId = $trip->id;

        $this->tripRepository->remove($trip, true);

        $this->tripReadModelProvider->invalidate($tripId);

        $this->addFlash('success', 'trip.deleted_successfully');

        return $this->redirectToRoute(IndexTripController::ROUTE, [], Response::HTTP_SEE_OTHER);
    }
}
