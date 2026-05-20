<?php

declare(strict_types=1);

namespace App\Controller\SurfSession;

use App\Entity\SurfSession;
use App\Entity\User;
use App\Enum\User\UserRole;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use App\Form\SurfSession\SurfSessionFormType;
use App\Repository\SurfSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class NewSurfSessionController extends AbstractController
{
    public function __construct(
        private readonly ObjectMapperInterface $objectMapper,
        private readonly SurfSessionRepository $surfSessionRepository,
    ) {}

    #[Route(
        path: '/sessions/new',
        name: 'app.surf_session.new',
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    #[IsGranted(UserRole::USER)]
    public function __invoke(Request $request, #[CurrentUser()] User $currentUser): Response
    {
        $surfSessionWriteModel = new SurfSessionWriteModel();

        $form = $this->createForm(SurfSessionFormType::class, $surfSessionWriteModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $surfSession = $this->objectMapper->map($surfSessionWriteModel, SurfSession::class);
            $surfSession->user = $currentUser;

            $this->surfSessionRepository->save($surfSession, true);

            $this->addFlash('success', 'surf_session.created_successfully');

            return $this->redirectToRoute('app.surf_session.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('surf_session/new.html.twig', [
            'form' => $form,
        ]);
    }
}
