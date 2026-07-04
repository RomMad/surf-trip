<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Enum\User\UserRole;
use App\Form\User\AvatarFormType;
use App\Repository\UserRepository;
use App\Service\Image\AvatarManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRole::USER)]
final class AvatarController extends AbstractController
{
    public const string ROUTE = 'app.user.avatar';

    public function __construct(
        private readonly AvatarManager $avatarManager,
        private readonly UserRepository $userRepository,
    ) {}

    #[Route(
        path: '/profile/avatar',
        name: self::ROUTE,
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    public function __invoke(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $form = $this->createForm(AvatarFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->avatarManager->upload($currentUser, $form->get('avatar')->getData());

            $this->userRepository->save($currentUser, true);

            $this->addFlash('success', 'user.avatar.updated_successfully');

            return $this->redirectToRoute(EditProfileController::ROUTE, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/avatar/_form_frame.html.twig', [
            'form' => $form,
        ]);
    }
}
