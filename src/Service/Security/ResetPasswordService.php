<?php

declare(strict_types=1);

namespace App\Service\Security;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

final class ResetPasswordService
{
    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
    ) {}

    public function processSendingPasswordResetEmail(Email $email): ?ResetPasswordToken
    {
        /** @var User|null */
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        if (!$user) {
            return null;
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return null;
        }

        $this->sendResetPasswordEmail($user, $resetToken);

        return $resetToken;
    }

    private function sendResetPasswordEmail(User $user, ResetPasswordToken $resetToken): void
    {
        $email = new TemplatedEmail()
            ->to($user->email->value)
            ->subject($this->translator->trans('security.reset_password.request.email.subject'))
            ->htmlTemplate('security/reset_password/email.html.twig')
            ->context([
                'reset_token' => $resetToken,
            ])
        ;

        $this->mailer->send($email);
    }
}
