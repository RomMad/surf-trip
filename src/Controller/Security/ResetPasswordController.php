<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Controller\Trip\IndexTripController;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

final class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public const string ROUTE = 'app.reset_password';

    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route(
        path: '/reset-password/reset/{token}',
        name: self::ROUTE,
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    public function __invoke(Request $request, ?string $token = null): Response
    {
        if (null !== $token) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute(self::ROUTE);
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $this->translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute(RequestResetPasswordController::ROUTE);
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);

            $plainPassword = $form->get('plainPassword')->getData();
            $user->password = $this->passwordHasher->hashPassword($user, $plainPassword);

            $this->entityManager->flush();

            $this->cleanSessionAfterReset();

            return $this->redirectToRoute(IndexTripController::ROUTE);
        }

        return $this->render('security/reset_password/reset.html.twig', [
            'form' => $form,
        ]);
    }
}
