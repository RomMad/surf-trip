<?php

namespace App\Controller;

use App\Entity\Trip;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class ShowTripController extends AbstractController
{
    #[Route(
        path: '/trip/{id:trip}',
        name: 'app.trip.show',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_GET]
    )]
    public function __invoke(Trip $trip): Response
    {
        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }
}
