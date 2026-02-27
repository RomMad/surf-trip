<?php

declare(strict_types=1);

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'email.label',
            ])
            ->add('firstName', null, [
                'label' => 'first_name.label',
            ])
            ->add('lastName', null, [
                'label' => 'last_name.label',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'security.registration.password.invalid',
                'required' => true,
                'first_options' => [
                    'label' => 'password.label',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                    'constraints' => [
                        new NotBlank(message: 'security.registration.password.not_blank'),
                        new Length(
                            min: 6,
                            max: 50,
                            minMessage: 'security.registration.password.min_length',
                            maxMessage: 'security.registration.password.max_length',
                        ),
                    ],
                ],
                'second_options' => [
                    'label' => 'password_repeat.label',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'security.registration.agree_terms.label',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(message: 'security.registration.agree_terms.is_true'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
