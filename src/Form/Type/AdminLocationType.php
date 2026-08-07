<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Embeddable\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AdminLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', null, [
                'label' => 'location.label',
            ])
            ->add('comment', null, [
                'label' => 'location.comment.label',
            ])
            ->add('latitude', null, [
                'label' => 'location.latitude.label',
                'required' => true,
            ])
            ->add('longitude', null, [
                'label' => 'location.longitude.label',
                'required' => true,
            ])
            ->add('placeId', null, [
                'label' => 'location.placeId.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Location::class,
            ])
        ;
    }
}
