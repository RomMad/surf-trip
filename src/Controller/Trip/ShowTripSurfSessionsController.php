<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Entity\User;
use App\Pagination\SurfSessionPager;
use App\Security\Voter\TripVoter;
use App\Service\Trip\TripReadModelProvider;
use App\Turbo\Frame\TripFrameId;
use App\Turbo\Http\TurboHeader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\Turbo\TurboStreamResponse;

final class ShowTripSurfSessionsController extends AbstractController
{
    public const string ROUTE = 'app.trip.surf_sessions';

    public function __construct(
        private readonly TripReadModelProvider $tripReadModelProvider,
        private readonly SurfSessionPager $surfSessionPager,
    ) {}

    #[Route(
        path: '/trip/{id}/surf-sessions/section',
        name: self::ROUTE,
        requirements: [
            'id' => Requirement::POSITIVE_INT,
        ],
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(Request $request, int $id, #[CurrentUser()] User $currentUser): Response
    {
        $trip = $this->tripReadModelProvider->getById($id);

        $this->denyAccessUnlessGranted(TripVoter::EDIT, $trip);

        $pager = $this->surfSessionPager->createForTrip($request, $currentUser, $trip->id, 6);

        $view = TripFrameId::SURF_SESSIONS_PAGE_CONTENT === $request->headers->get(TurboHeader::FRAME)
            ? 'trip/_surf_sessions/_stream.html.twig'
            : 'trip/_surf_sessions/_section.html.twig';

        return $this->render($view, [
            'trip' => $trip,
            'pager' => $pager,
        ], new TurboStreamResponse());
    }
}
