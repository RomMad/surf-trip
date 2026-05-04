<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Enum\User\Locale;
use App\Enum\User\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SetUserLocaleController extends AbstractController
{
    public const string ROUTE = 'app.user.locale.set';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route(
        path: '/locale',
        name: self::ROUTE,
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(UserRole::USER)]
    public function __invoke(
        Request $request,
        #[CurrentUser()]
        User $currentUser,
        #[MapQueryParameter()]
        string $target
    ): RedirectResponse {
        $currentUser->locale = Locale::from($request->getLocale());

        $this->entityManager->flush();

        return $this->redirect($target, Response::HTTP_SEE_OTHER);
    }
}
