<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\Trip\RequiredLevel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $trip = $builder->getData();

        $builder
            ->add('title', null, [
                'label' => 'title.label',
                'empty_data' => '',
            ])
            ->add('location', null, [
                'label' => 'location.label',
                'empty_data' => '',
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
                'autocomplete' => true,
            ])
            ->add('description', null, [
                'label' => 'description.label',
            ])
        ;

        if ($trip instanceof Trip && null !== $trip->getId()) {
            $builder->add('owners', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'label' => 'owners.label',
                'multiple' => true,
                'autocomplete' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
