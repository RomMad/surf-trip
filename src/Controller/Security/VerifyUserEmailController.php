<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Controller\Trip\IndexTripController;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class VerifyUserEmailController extends AbstractController
{
    public const string ROUTE = 'app.verify_email';

    public function __construct(
        private readonly EmailVerifier $emailVerifier,
        private readonly TranslatorInterface $translator,
        private readonly UserRepository $userRepository,
    ) {}

    #[Route('/verify/email', name: self::ROUTE)]
    public function __invoke(Request $request): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute(RegisterController::ROUTE);
        }

        $user = $this->userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute(RegisterController::ROUTE);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $verifyEmailException) {
            $this->addFlash(
                'verify_email_error',
                $this->translator->trans($verifyEmailException->getReason(), [], 'VerifyEmailBundle')
            );

            return $this->redirectToRoute(RegisterController::ROUTE);
        }

        $this->addFlash('success', 'security.email_verified_successfully');

        return $this->redirectToRoute(IndexTripController::ROUTE);
    }
}
