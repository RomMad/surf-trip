<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Model\User\RegistrationWriteModel;
use App\Form\Security\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Security\RegistrationConfirmationEmailSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    public const string ROUTE = 'app.register';

    public function __construct(
        private readonly RegistrationConfirmationEmailSender $registrationConfirmationEmailSender,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly Security $security,
        private readonly UserRepository $userRepository,
        private readonly ObjectMapperInterface $objectMapper,
    ) {}

    #[Route('/register', name: self::ROUTE, methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function __invoke(Request $request): Response
    {
        $registrationWriteModel = new RegistrationWriteModel();

        $form = $this->createForm(RegistrationFormType::class, $registrationWriteModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->objectMapper->map($registrationWriteModel, User::class);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->password = $this->userPasswordHasher->hashPassword($user, $plainPassword);

            $this->userRepository->save($user, true);

            $this->registrationConfirmationEmailSender->send($user);

            return $this->security->login($user, AppAuthenticator::class, 'main');
        }

        return $this->render('security/registration/register.html.twig', [
            'form' => $form,
        ]);
    }
}
