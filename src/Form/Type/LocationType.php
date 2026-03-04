<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ValueObject\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LocationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Location::class,
            'label' => 'location.label',
            'empty_data' => '',
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return ValueObjectType::class;
    }
}
