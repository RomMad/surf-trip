<?php

namespace App\Form;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\RequiredLevel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'title.label',
            ])
            ->add('location', null, [
                'label' => 'location.label',
            ])
            ->add('startAt', null, [
                'label' => 'start_at.label',
                'widget' => 'single_text',
            ])
            ->add('endAt', null, [
                'label' => 'end_at.label',
                'widget' => 'single_text',
            ])
            ->add('requiredLevels', EnumType::class, [
                'class' => RequiredLevel::class,
                'label' => 'required_levels.label',
                'multiple' => true,
            ])
            ->add('description', null, [
                'label' => 'description.label',
            ])
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'owner.label',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
