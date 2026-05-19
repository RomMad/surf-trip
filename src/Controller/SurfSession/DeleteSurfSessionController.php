<?php

declare(strict_types=1);

namespace App\Controller\SurfSession;

use App\Entity\SurfSession;
use App\Enum\User\UserRole;
use App\Repository\SurfSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRole::USER)]
final class DeleteSurfSessionController extends AbstractController
{
    public function __construct(
        private readonly SurfSessionRepository $surfSessionRepository,
    ) {}

    #[Route(
        path: '/sessions/{id:surfSession}/delete',
        name: 'app.surf_session.delete',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_POST],
    )]
    #[IsCsrfTokenValid(new Expression('"delete" ~ args["surfSession"].id'))]
    public function __invoke(SurfSession $surfSession): Response
    {
        $this->surfSessionRepository->remove($surfSession, true);

        $this->addFlash('success', 'surf_session.deleted_successfully');

        return $this->redirectToRoute('app.surf_session.index', [], Response::HTTP_SEE_OTHER);
    }
}
