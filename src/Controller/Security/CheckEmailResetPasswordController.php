<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

final class CheckEmailResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public const string ROUTE = 'app.check_email';

    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
    ) {}

    #[Route(
        path: '/reset-password/check-email',
        name: self::ROUTE,
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(): Response
    {
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('security/reset_password/check_email.html.twig', [
            'reset_token' => $resetToken,
        ]);
    }
}
