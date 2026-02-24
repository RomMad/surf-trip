<?php

namespace App\Controller;

use App\Pagination\PagerFactory;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexTripController extends AbstractController
{
    public const string ROUTE = 'app.trip.index';

    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly PagerFactory $pagerFactory,
    ) {}

    #[Route(
        path: '/trips',
        name: self::ROUTE,
        methods: [Request::METHOD_GET],
    )]
    #[Route(path: '/', methods: [Request::METHOD_GET])]
    public function index(Request $request): Response
    {
        $queryBuilder = $this->tripRepository->createOrderedQueryBuilder();
        $pager = $this->pagerFactory->create($queryBuilder, $request, 10);

        return $this->render('trip/index.html.twig', [
            'pager' => $pager,
        ]);
    }
}
