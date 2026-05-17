<?php

declare(strict_types=1);

namespace App\Controller\SurfSession;

use App\Enum\User\UserRole;
use App\Pagination\SurfSessionPager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRole::USER)]
final class IndexSurfSessionController extends AbstractController
{
    public const string ROUTE = 'app.surf_session.index';

    public function __construct(
        private readonly SurfSessionPager $surfSessionPager,
    ) {}

    #[Route(
        path: '/sessions',
        name: self::ROUTE,
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(Request $request): Response
    {
        $pager = $this->surfSessionPager->create($request);

        return $this->render('surf_session/index.html.twig', [
            'pager' => $pager,
        ]);
    }
}
