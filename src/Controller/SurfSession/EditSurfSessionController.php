<?php

declare(strict_types=1);

namespace App\Controller\SurfSession;

use App\Entity\SurfSession;
use App\Enum\User\UserRole;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use App\Form\SurfSession\SurfSessionFormType;
use App\Repository\SurfSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRole::USER)]
final class EditSurfSessionController extends AbstractController
{
    public function __construct(
        private readonly ObjectMapperInterface $objectMapper,
        private readonly SurfSessionRepository $surfSessionRepository,
    ) {}

    #[Route(
        path: '/sessions/{id:surfSession}/edit',
        name: 'app.surf_session.edit',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    public function __invoke(Request $request, SurfSession $surfSession): Response
    {
        $surfSessionWriteModel = $this->objectMapper->map($surfSession, SurfSessionWriteModel::class);

        $form = $this->createForm(SurfSessionFormType::class, $surfSessionWriteModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->objectMapper->map($surfSessionWriteModel, $surfSession);

            $this->surfSessionRepository->save($surfSession, true);

            $this->addFlash('success', 'surf_session.updated_successfully');

            return $this->redirectToRoute('app.surf_session.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('surf_session/edit.html.twig', [
            'session' => $surfSession,
            'form' => $form,
        ]);
    }
}
