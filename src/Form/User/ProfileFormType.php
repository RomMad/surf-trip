<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Enum\User\SurfLevel;
use App\Form\Model\User\ProfileWriteModel;
use App\Form\Type\EmailType;
use App\Form\Type\FirstNameType;
use App\Form\Type\LastNameType;
use App\Form\Type\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

final class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', UsernameType::class)
            ->add('firstName', FirstNameType::class)
            ->add('lastName', LastNameType::class)
            ->add('avatar', FileType::class, [
                'label' => 'avatar.label',
                'constraints' => [
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
                'required' => false,
            ])
            ->add('level', EnumType::class, [
                'class' => SurfLevel::class,
                'label' => 'surf_level.label',
                'required' => false,
            ])
            ->add('location', null, [
                'label' => 'location.label',
            ])
            ->add('instagram', null, [
                'label' => 'instagram.label',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description.label',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfileWriteModel::class,
        ]);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'profile';
    }
}
