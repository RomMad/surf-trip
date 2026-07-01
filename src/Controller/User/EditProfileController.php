<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Enum\User\UserRole;
use App\Form\Model\User\ProfileWriteModel;
use App\Form\User\ProfileFormType;
use App\Repository\UserRepository;
use App\Service\Image\AvatarManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditProfileController extends AbstractController
{
    public const string ROUTE = 'app.user.profile.edit';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ObjectMapperInterface $objectMapper,
        private readonly AvatarManager $avatarManager,
    ) {}

    #[Route(
        path: '/profile/edit',
        name: self::ROUTE,
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    #[IsGranted(UserRole::USER)]
    public function __invoke(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $profileWriteModel = $this->objectMapper->map($currentUser, ProfileWriteModel::class);

        $form = $this->createForm(ProfileFormType::class, $profileWriteModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->objectMapper->map($profileWriteModel, $currentUser);
            $this->avatarManager->upload($currentUser, $form->get('avatar')->getData());

            $this->userRepository->save($currentUser, true);

            $this->addFlash('success', 'user.profile.updated_successfully');

            return $this->redirectToRoute(self::ROUTE, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit_profile.html.twig', [
            'form' => $form,
        ]);
    }
}
