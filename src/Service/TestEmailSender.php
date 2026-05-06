<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;

final readonly class TestEmailSender
{
    public function __construct(
        private MailerInterface $mailer,
        private Security $security,
    ) {}

    public function send(
        string $subject = 'Test email',
        string $message = 'This is a test email.',
        string $to = 'test@surftrip.com',
    ): void {
        $user = $this->security->getUser();
        $locale = null;

        if ($user instanceof User) {
            $to = $user->email->value;
            $locale = $user->locale->value;
        }

        $email = new TemplatedEmail()
            ->to($to)
            ->subject($subject)
            ->htmlTemplate('emails/test_email.html.twig')
            ->context([
                'message' => $message,
            ])
            ->locale($locale)
        ;

        $this->mailer->send($email);
    }
}
