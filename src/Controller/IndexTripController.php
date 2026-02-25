<?php

namespace App\Controller;

use App\Form\Model\TripFilter;
use App\Form\TripFilterFormType;
use App\Pagination\TripPager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexTripController extends AbstractController
{
    public const string ROUTE = 'app.trip.index';

    public function __construct(
        private readonly TripPager $tripPager,
    ) {}

    #[Route(
        path: '/trips',
        name: self::ROUTE,
        methods: [Request::METHOD_GET],
    )]
    #[Route(path: '/', methods: [Request::METHOD_GET])]
    public function __invoke(Request $request): Response
    {
        $filter = new TripFilter();
        $form = $this->createForm(TripFilterFormType::class, $filter);
        $form->handleRequest($request);

        $pager = $this->tripPager->create($filter, $request, 10);

        if ('trip_results' === $request->headers->get('Turbo-Frame')) {
            return $this->renderBlock('trip/index.html.twig', 'trip_results', [
                'pager' => $pager,
            ]);
        }

        return $this->render('trip/index.html.twig', [
            'pager' => $pager,
            'form' => $form,
        ]);
    }
}
