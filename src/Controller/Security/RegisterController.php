<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Security\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\RegistrationConfirmationEmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    public const string ROUTE = 'app.register';

    public function __construct(
        private readonly RegistrationConfirmationEmailSender $registrationConfirmationEmailSender,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('/register', name: self::ROUTE)]
    public function __invoke(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->registrationConfirmationEmailSender->send($user);

            return $this->security->login($user, AppAuthenticator::class, 'main');
        }

        return $this->render('security/registration/register.html.twig', [
            'form' => $form,
        ]);
    }
}
