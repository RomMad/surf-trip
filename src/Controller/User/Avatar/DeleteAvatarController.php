<?php

declare(strict_types=1);

namespace App\Controller\User\Avatar;

use App\Entity\User;
use App\Enum\User\UserRole;
use App\Repository\UserRepository;
use App\Service\Image\AvatarManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboStreamResponse;

#[IsGranted(UserRole::USER)]
final class DeleteAvatarController extends AbstractController
{
    public const string ROUTE = 'app.user.avatar.delete';

    public function __construct(
        private readonly AvatarManager $avatarManager,
        private readonly UserRepository $userRepository,
    ) {}

    #[Route(
        path: '/profile/avatar/delete',
        name: self::ROUTE,
        methods: [Request::METHOD_POST],
    )]
    #[IsCsrfTokenValid(new Expression('"avatar-delete" ~ args["currentUser"].id'))]
    public function __invoke(#[CurrentUser] User $currentUser): Response
    {
        $this->avatarManager->delete($currentUser);

        $this->userRepository->save($currentUser, true);

        return $this->render('user/avatar/_success_stream.html.twig', response: new TurboStreamResponse());
    }
}
