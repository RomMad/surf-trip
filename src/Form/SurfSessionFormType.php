<?php

declare(strict_types=1);

namespace App\Form;

use App\Enum\SurfSession\SurfSessionRating;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use App\Form\Type\DateTimeImmutableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SurfSessionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('spot', TextType::class, [
                'label' => 'surf_session.spot.label',
                'attr' => [
                    'placeholder' => 'surf_session.spot.placeholder',
                    'maxlength' => 100,
                ],
            ])
            ->add('board', TextType::class, [
                'label' => 'surf_session.board.label',
                'attr' => [
                    'placeholder' => 'surf_session.board.placeholder',
                    'maxlength' => 100,
                ],
                'required' => false,
            ])
            ->add('startAt', DateTimeImmutableType::class, [
                'label' => 'start_at.label',
            ])
            ->add('endAt', DateTimeImmutableType::class, [
                'label' => 'end_at.label',
            ])
            ->add('rating', EnumType::class, [
                'class' => SurfSessionRating::class,
                'label' => 'surf_session.rating.label',
                'placeholder' => 'surf_session.rating.placeholder',
                'expanded' => true,
                'required' => false,
            ])
            ->add('objective', TextareaType::class, [
                'label' => 'surf_session.objective.label',
                'attr' => [
                    'placeholder' => 'surf_session.objective.placeholder',
                    'maxlength' => 1000,
                    'rows' => 4,
                ],
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'surf_session.comment.label',
                'attr' => [
                    'placeholder' => 'surf_session.comment.placeholder',
                    'maxlength' => 5000,
                    'rows' => 6,
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SurfSessionWriteModel::class,
        ]);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'surf_session';
    }
}
