<?php

declare(strict_types=1);

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

final class AvatarFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'label' => 'avatar.label',
                'constraints' => [
                    new NotNull(message: 'user.avatar.required'),
                    new Image(
                        maxSize: '5M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                    ),
                ],
                'mapped' => false,
                'required' => true,
            ])
        ;
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'avatar';
    }
}
