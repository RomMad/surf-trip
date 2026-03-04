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
    public const string ROUTE = 'app.trip.edit';

    public function __construct(
        private readonly TripRepository $tripRepository,
    ) {}

    #[Route(
        path: '/trip/{id:trip}/{slug}/edit',
        name: self::ROUTE,
        requirements: [
            'id' => Requirement::POSITIVE_INT,
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    #[IsGranted(TripVoter::EDIT, subject: 'trip')]
    public function __invoke(Request $request, Trip $trip, string $slug): Response
    {
        if ($trip->slug->value !== $slug) {
            return $this->redirectToRoute(self::ROUTE, [
                'id' => $trip->id,
                'slug' => $trip->slug->value,
            ], Response::HTTP_SEE_OTHER);
        }

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
