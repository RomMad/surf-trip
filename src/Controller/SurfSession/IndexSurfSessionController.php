<?php

declare(strict_types=1);

namespace App\Controller\SurfSession;

use App\Entity\User;
use App\Enum\User\UserRole;
use App\Form\Model\SurfSession\SurfSessionSearchInput;
use App\Form\SurfSession\SurfSessionSearchFormType;
use App\Pagination\SurfSessionPager;
use App\Turbo\Frame\SurfSessionFrameId;
use App\Turbo\Http\TurboHeader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboStreamResponse;

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
    public function __invoke(Request $request, #[CurrentUser()] User $currentUser): Response
    {
        $searchInput = new SurfSessionSearchInput();
        $form = $this->createForm(SurfSessionSearchFormType::class, $searchInput);
        $form->handleRequest($request);

        $pager = $this->surfSessionPager->create($request, $currentUser, $searchInput);

        return match ($request->headers->get(TurboHeader::FRAME)) {
            SurfSessionFrameId::PAGE_CONTENT => $this->render('surf_session/_stream.html.twig', [
                'pager' => $pager,
            ], new TurboStreamResponse()),
            SurfSessionFrameId::RESULTS => $this->renderBlock('surf_session/index.html.twig', SurfSessionFrameId::RESULTS, [
                'pager' => $pager,
            ]),
            default => $this->render('surf_session/index.html.twig', [
                'pager' => $pager,
                'form' => $form,
            ]),
        };
    }
}
