<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\User\UserRole;
use App\Form\Model\TripWriteModel;
use App\Form\TripFormType;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class NewTripController extends AbstractController
{
    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly ObjectMapperInterface $objectMapper,
    ) {}

    #[Route(
        path: '/trip/new',
        name: 'app.trip.new',
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    #[IsGranted(UserRole::USER)]
    public function __invoke(Request $request, #[CurrentUser()] User $currentUser): Response
    {
        $tripWriteModel = new TripWriteModel();

        $form = $this->createForm(TripFormType::class, $tripWriteModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trip = $this->objectMapper->map($tripWriteModel, Trip::class);
            $trip->addOwner($currentUser);

            $this->tripRepository->save($trip, true);

            $this->addFlash('success', 'trip.created_successfully');

            return $this->redirectToRoute(IndexTripController::ROUTE, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/new.html.twig', [
            'trip' => $tripWriteModel,
            'form' => $form,
        ]);
    }
}
