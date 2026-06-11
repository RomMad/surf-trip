<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Form\Model\Trip\TripWriteModel;
use App\Form\Trip\TripFormType;
use App\Repository\TripRepository;
use App\Security\Voter\TripVoter;
use App\Service\Trip\TripUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class EditTripController extends AbstractController
{
    public const string ROUTE = 'app.trip.edit';

    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly ObjectMapperInterface $objectMapper,
        private readonly TripUpdater $tripUpdater,
    ) {}

    #[Route(
        path: '/trip/{id}/{slug}/edit',
        name: self::ROUTE,
        requirements: [
            'id' => Requirement::POSITIVE_INT,
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    public function __invoke(Request $request, int $id, string $slug): Response
    {
        $trip = $this->tripRepository->findOneByIdWithOwners($id);

        $this->denyAccessUnlessGranted(TripVoter::EDIT, $trip);

        if ($trip->slug->value !== $slug) {
            return $this->redirectToRoute(self::ROUTE, [
                'id' => $trip->id,
                'slug' => $trip->slug->value,
            ], Response::HTTP_SEE_OTHER);
        }

        $tripWriteModel = $this->objectMapper->map($trip, TripWriteModel::class);

        $form = $this->createForm(TripFormType::class, $tripWriteModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tripUpdater->updateFromWriteModel($id, $tripWriteModel);

            $this->addFlash('success', 'trip.updated_successfully');

            return $this->redirectToRoute(IndexTripController::ROUTE, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }
}
