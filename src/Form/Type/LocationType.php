<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\Shared\LocationInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'location.label',
                'attr' => [
                    'placeholder' => 'location.placeholder',
                    'data-location-autocomplete-target' => 'label',
                ],
            ])
            ->add('latitude', HiddenType::class, [
                'attr' => [
                    'data-location-autocomplete-target' => 'latitude',
                ],
            ])
            ->add('longitude', HiddenType::class, [
                'attr' => [
                    'data-location-autocomplete-target' => 'longitude',
                ],
            ])
            ->add('placeId', HiddenType::class, [
                'attr' => [
                    'data-location-autocomplete-target' => 'placeId',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => false,
                'data_class' => LocationInput::class,
            ])
        ;
    }
}
