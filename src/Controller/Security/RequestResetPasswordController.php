<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\ValueObject\Email;
use App\Form\ResetPasswordRequestFormType;
use App\Service\Security\ResetPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

final class RequestResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public const string ROUTE = 'app.forgot_password_request';

    public function __construct(
        private readonly ResetPasswordService $resetPasswordService,
    ) {}

    #[Route(
        path: '/reset-password',
        name: self::ROUTE,
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = Email::from($form->get('email')->getData());
            $resetToken = $this->resetPasswordService->processSendingPasswordResetEmail($email);

            if (!$resetToken instanceof ResetPasswordToken) {
                return $this->redirectToRoute(CheckEmailResetPasswordController::ROUTE);
            }

            $this->setTokenObjectInSession($resetToken);

            return $this->redirectToRoute(CheckEmailResetPasswordController::ROUTE);
        }

        return $this->render('security/reset_password/request.html.twig', [
            'form' => $form,
        ]);
    }
}
