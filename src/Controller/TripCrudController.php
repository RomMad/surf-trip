<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class TripCrudController extends AbstractController
{
    public const string TRIP_INDEX_ROUTE = 'app.trip.index';

    public function __construct(
        private readonly TripRepository $tripRepository
    ) {}

    #[Route(
        path: '/trips',
        name: self::TRIP_INDEX_ROUTE,
        methods: [Request::METHOD_GET]
    )]
    public function index(): Response
    {
        return $this->render('trip/index.html.twig', [
            'trips' => $this->tripRepository->findAll(),
        ]);
    }

    #[Route(
        path: '/trip/new',
        name: 'app.trip.new',
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function new(Request $request): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripFormType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trip->setCreatedAt(new \DateTimeImmutable());
            $this->tripRepository->save($trip, true);

            return $this->redirectToRoute(self::TRIP_INDEX_ROUTE, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/new.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route(
        path: '/trip/{id:trip}',
        name: 'app.trip.show',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_GET]
    )]
    public function show(Trip $trip): Response
    {
        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route(
        path: '/trip/{id:trip}/edit',
        name: 'app.trip.edit',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    public function edit(Request $request, Trip $trip): Response
    {
        $form = $this->createForm(TripFormType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tripRepository->save($trip, true);

            return $this->redirectToRoute(self::TRIP_INDEX_ROUTE, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route(
        path: '/trip/{id:trip}/delete',
        name: 'app.trip.delete',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_POST]
    )]
    #[IsCsrfTokenValid(new Expression('"delete" ~ args["trip"].getId()'))]
    public function delete(Trip $trip): Response
    {
        dump($trip);
        $this->tripRepository->remove($trip, true);

        return $this->redirectToRoute(self::TRIP_INDEX_ROUTE, [], Response::HTTP_SEE_OTHER);
    }
}
