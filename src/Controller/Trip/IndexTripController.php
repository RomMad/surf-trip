<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Entity\User;
use App\Form\Model\Trip\TripSearchInput;
use App\Form\Trip\TripSearchFormType;
use App\Pagination\TripPager;
use App\Turbo\Frame\TripFrameId;
use App\Turbo\Http\TurboHeader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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
    public function __invoke(Request $request, #[CurrentUser()] ?User $user = null): Response
    {
        $searchInput = new TripSearchInput();
        $form = $this->createForm(TripSearchFormType::class, $searchInput);
        $form->handleRequest($request);

        $pager = $this->tripPager->create($searchInput, $request, $user, 10);

        if (TripFrameId::RESULTS === $request->headers->get(TurboHeader::FRAME)) {
            return $this->renderBlock('trip/index.html.twig', TripFrameId::RESULTS, [
                'pager' => $pager,
            ]);
        }

        return $this->render('trip/index.html.twig', [
            'pager' => $pager,
            'form' => $form,
        ]);
    }
}
