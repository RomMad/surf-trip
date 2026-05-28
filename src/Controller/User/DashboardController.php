<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Enum\User\UserRole;
use App\Service\Dashboard\Chart\DashboardChartsFactory;
use App\Service\UserDashboardStatsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRole::USER)]
final class DashboardController extends AbstractController
{
    public const string ROUTE = 'app.me.dashboard';

    public function __construct(
        private readonly UserDashboardStatsProvider $userDashboardStatsProvider,
        private readonly DashboardChartsFactory $dashboardChartsFactory,
    ) {}

    #[Route(
        path: '/me/dashboard',
        name: self::ROUTE,
        methods: [Request::METHOD_GET],
    )]
    #[Route(
        path: '/',
        name: 'app.home',
        methods: [Request::METHOD_GET]
    )]
    public function __invoke(#[CurrentUser] User $currentUser): Response
    {
        $stats = $this->userDashboardStatsProvider->getForUser($currentUser);
        $charts = $this->dashboardChartsFactory->createAll($stats, $currentUser->locale);

        return $this->render('user/dashboard.html.twig', [
            'stats' => $stats,
            'charts' => $charts,
        ]);
    }
}
