<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\TripRepository;
use App\Security\Voter\TripVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditTripController extends AbstractController
{
    public function __construct(
        private readonly TripRepository $tripRepository,
    ) {}

    #[Route(
        path: '/trip/{id:trip}/edit',
        name: 'app.trip.edit',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    #[IsGranted(TripVoter::EDIT, subject: 'trip')]
    public function __invoke(Request $request, Trip $trip): Response
    {
        $form = $this->createForm(TripFormType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tripRepository->save($trip, true);

            $this->addFlash('success', 'trip.updated_successfully');

            return $this->redirectToRoute(IndexTripController::ROUTE, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }
}
