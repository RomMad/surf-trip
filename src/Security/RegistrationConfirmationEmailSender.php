<?php

declare(strict_types=1);

namespace App\Security;

use App\Controller\Security\VerifyUserEmailController;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class RegistrationConfirmationEmailSender
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private TranslatorInterface $translator,
    ) {}

    public function send(User $user): void
    {
        $this->emailVerifier->sendEmailConfirmation(
            VerifyUserEmailController::ROUTE,
            $user,
            (new TemplatedEmail())
                ->to((string) $user->getEmail())
                ->subject($this->translator->trans('security.registration.confirm_email.subject'))
                ->htmlTemplate('security/registration/confirmation_email.html.twig')
        );
    }
}
