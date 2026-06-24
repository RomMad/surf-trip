<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Entity\User;
use App\Form\Model\Trip\TripSearchInput;
use App\Form\Trip\TripSearchFormType;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class MapTripController extends AbstractController
{
    public const string ROUTE = 'app.trip.map';

    public function __construct(
        private readonly TripRepository $tripRepository,
    ) {}

    #[Route(
        path: '/trips/map',
        name: self::ROUTE,
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(Request $request, #[CurrentUser()] ?User $user = null): Response
    {
        $searchInput = new TripSearchInput();
        $form = $this->createForm(TripSearchFormType::class, $searchInput, [
            'is_authenticated' => null !== $user,
        ]);
        $form->handleRequest($request);

        $trips = $this->tripRepository->findMapTrips($searchInput, $user);

        return $this->render('trip/map.html.twig', [
            'form' => $form,
            'trips' => $trips,
        ]);
    }
}
